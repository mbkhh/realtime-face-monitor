from flask import Flask, Response
import cv2
import json
import os
from datetime import datetime
import mysql.connector
import threading  
import time      

# --- App and Camera Setup ---
app = Flask(__name__)
camera = cv2.VideoCapture(1, cv2.CAP_V4L2)
face_cascade = cv2.CascadeClassifier('haarcascade_frontalface_default.xml')

SETTINGS_FILE_PATH = "/var/www/html/settings.json"
DEFAULT_BORDER_COLOR_HEX = '#22c55e'


# --- Thread-Safe Shared Resources ---
frame_lock = threading.Lock()
latest_frame = None  # The monitoring thread will put frames here.
latest_faces = []    # The monitoring thread will put detected face coordinates here.

# --- Directory and DB Config---
LOG_DIR = "/var/www/html/detection_logs"
if not os.path.exists(LOG_DIR):
    os.makedirs(LOG_DIR)
    print(f"Created directory: {LOG_DIR}")

DB_CONFIG = {
    'user': 'appuser',
    'password': 'appuser',
    'host': 'localhost',
    'database': 'monitoring_system'
}
def hex_to_bgr(hex_color):
    """Converts a hex color string (e.g., '#ffffff') to a BGR tuple."""
    hex_color = hex_color.lstrip('#')
    if len(hex_color) == 3:
        hex_color = "".join([c*2 for c in hex_color])
    if len(hex_color) != 6:
        return (0, 255, 0) 
    
    rgb = tuple(int(hex_color[i:i+2], 16) for i in (0, 2, 4))
    return (rgb[2], rgb[1], rgb[0]) # Return as BGR
def load_settings():
    """Reads the border color from the JSON file and updates the global variable."""
    global border_color_bgr
    try:
        if os.path.exists(SETTINGS_FILE_PATH):
            with open(SETTINGS_FILE_PATH, 'r') as f:
                data = json.load(f)
                color_hex = data.get('borderColor', DEFAULT_BORDER_COLOR_HEX)
                border_color_bgr = hex_to_bgr(color_hex)
                print(f"[Settings] Loaded border color: {color_hex} -> {border_color_bgr}")
        else:
            border_color_bgr = hex_to_bgr(DEFAULT_BORDER_COLOR_HEX)
    except (json.JSONDecodeError, IOError) as e:
        print(f"[Settings] Error loading settings file: {e}. Using default color.")
        border_color_bgr = hex_to_bgr(DEFAULT_BORDER_COLOR_HEX)


def log_detection(filename):
    """Saves the detection event to the MariaDB database. (Unchanged)"""
    try:
        conn = mysql.connector.connect(**DB_CONFIG)
        cursor = conn.cursor()
        sql = "INSERT INTO camera_detections (filename) VALUES (%s)"
        val = (filename,)
        cursor.execute(sql, val)
        conn.commit()
        print(f"Logged to DB: {filename} - {cursor.rowcount} record inserted.")
    except mysql.connector.Error as err:
        print(f"Database Error: {err}")
    finally:
        if 'conn' in locals() and conn.is_connected():
            cursor.close()
            conn.close()

def monitor_and_detect():
    """
    This is the core function for my background thread. It runs continuously.
    """
    global latest_frame, latest_faces, frame_lock
    
    person_detected_in_last_frame = False
    frame_count = 0
    scale = 0.5
    
    print("[Monitoring Thread] Starting...")
    while True:
        if frame_count % 150 == 0:
            load_settings()
        success, frame = camera.read()
        if not success:
            print("[Monitoring Thread] Failed to read from camera.")
            time.sleep(1)
            continue
            
        # --- Share the latest frame with the streaming function ---
        with frame_lock:
            latest_frame = frame.copy()

        # --- Face Detection & Logging Logic (moved here) ---
        frame_count += 1
        if frame_count % 5 == 0:
            original_frame_for_saving = frame.copy()
            small_frame = cv2.resize(frame, (0, 0), fx=scale, fy=scale)
            gray = cv2.cvtColor(small_frame, cv2.COLOR_BGR2GRAY)
            faces = face_cascade.detectMultiScale(
                gray, scaleFactor=1.1, minNeighbors=5, minSize=(30, 30)
            )
            
            # Share the detected face coordinates for the live stream to use
            with frame_lock:
                latest_faces = faces

            if len(faces) > 0:
                if not person_detected_in_last_frame:
                    print("[Monitoring Thread] New person detected! Logging event...")
                    timestamp = datetime.now().strftime("%Y-%m-%d_%H-%M-%S")
                    filename = f"detection_{timestamp}.jpg"
                    filepath = os.path.join(LOG_DIR, filename)
                    
                    # Draw rectangles on the SAVED frame
                    for (x, y, w, h) in faces:
                        x, y, w, h = [int(v / scale) for v in (x, y, w, h)]
                        print(border_color_bgr)
                        cv2.rectangle(original_frame_for_saving, (x, y), (x + w, y + h), border_color_bgr, 2)
                        
                    cv2.imwrite(filepath, original_frame_for_saving)
                    print(f"[Monitoring Thread] Saved image: {filepath}")
                    log_detection(filename)
                    person_detected_in_last_frame = True
            else:
                if person_detected_in_last_frame:
                    print("[Monitoring Thread] Person has left. System is reset.")
                person_detected_in_last_frame = False

def generate_frames_for_stream():
    """
    This function is now very simple. It just grabs the latest frame
    and face data from the global variables and streams it.
    """
    global latest_frame, latest_faces, frame_lock
    scale = 0.5 
    
    while True:
        time.sleep(0.05) 
        
        with frame_lock: 
            if latest_frame is None:
                continue
            frame = latest_frame.copy()
            faces = latest_faces

        for (x, y, w, h) in faces:
            x, y, w, h = [int(v / scale) for v in (x, y, w, h)]
            cv2.rectangle(frame, (x, y), (x + w, y + h), border_color_bgr, 2)
            
        # Encode and stream the frame
        ret, buffer = cv2.imencode('.jpg', frame)
        frame_bytes = buffer.tobytes()
        yield (b'--frame\r\n'
               b'Content-Type: image/jpeg\r\n\r\n' + frame_bytes + b'\r\n')

# --- Flask Routes ---
@app.route('/video')
def video():
    return Response(generate_frames_for_stream(),
                    mimetype='multipart/x-mixed-replace; boundary=frame')

@app.route('/')
def index():
    return '<h1>Orange Pi Camera</h1><img src="/video" width="640" height="480">'

if __name__ == "__main__":
    monitor_thread = threading.Thread(target=monitor_and_detect, daemon=True)
    monitor_thread.start()
    
    # --- Start the Flask Web Server ---
    app.run(host="0.0.0.0", port=5000)
