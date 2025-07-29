import requests # The library for making HTTP requests
import os       # The library for interacting with the operating system (like creating folders)

# The URL for TheCatAPI, which gives us random cat pictures.
# The 'limit=10' part asks for 10 unique images at once.
API_URL = "https://api.thecatapi.com/v1/images/search?limit=10"

# The name of the folder where we will save the images.
FOLDER_NAME = "kittens"

def download_kitten_images():
    """
    Fetches 10 random kitten images from an API and saves them to a folder.
    """
    print("Connecting to the kitten factory...")

    # --- Create the folder if it doesn't already exist ---
    if not os.path.exists(FOLDER_NAME):
        os.makedirs(FOLDER_NAME)
        print(f"Created a folder named '{FOLDER_NAME}' to store the kittens.")

    try:
        # --- Make the request to the API ---
        response = requests.get(API_URL)
        # Raise an error if the request was unsuccessful (e.g., 404, 500)
        response.raise_for_status()

        # --- Get the data from the response ---
        # The API returns data in a format called JSON. We need to decode it.
        data = response.json()

        print(f"Found {len(data)} kittens! Starting download...")

        # --- Loop through each image entry from the API data ---
        for i, cat_info in enumerate(data):
            image_url = cat_info['url']
            # Get the file extension (usually .jpg or .png) from the URL
            file_extension = os.path.splitext(image_url)[1]
            file_name = f"kitten_{i+1}{file_extension}"
            file_path = os.path.join(FOLDER_NAME, file_name)

            # --- Download the image content ---
            print(f"Downloading {file_name} from {image_url}...")
            image_response = requests.get(image_url)
            image_response.raise_for_status()

            # --- Save the image to the file ---
            with open(file_path, 'wb') as f:
                f.write(image_response.content)

        print("\nAll done! Your 10 kittens are safe in the 'kittens' folder.")

    except requests.exceptions.RequestException as e:
        print(f"An error occurred with the network request: {e}")
    except Exception as e:
        print(f"An unexpected error occurred: {e}")

# --- This line makes the script run when you execute the file ---
if __name__ == "__main__":
    download_kitten_images()
