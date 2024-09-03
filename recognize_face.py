import face_recognition
import numpy as np
import sys

def load_face_encoding(encoding_str):
    return np.fromstring(encoding_str, sep=',')

def recognize_face(known_face_encoding, image_path):
    # Load image and detect faces
    unknown_image = face_recognition.load_image_file(image_path)
    unknown_face_encodings = face_recognition.face_encodings(unknown_image)
    
    for unknown_face_encoding in unknown_face_encodings:
        results = face_recognition.compare_faces([known_face_encoding], unknown_face_encoding)
        if True in results:
            return "recognized"
    return "not recognized"

def main():
    image_path = sys.argv[1]
    face_encoding_file = sys.argv[2]

    print("Image path:", image_path)
    print("Face encoding file:", face_encoding_file)

    # Read the face encoding string from the file
    with open(face_encoding_file, 'r') as f:
        face_encoding_str = f.read()

    # Load the known face encoding from the string
    known_face_encoding = load_face_encoding(face_encoding_str)

    # Recognize face
    result = recognize_face(known_face_encoding, image_path)
    print(result)

if __name__ == "__main__":
    main()