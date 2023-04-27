# UM PDF Convert Image
Extension to Ultimate Member for converting first page of an uploaded PDF file to an Image.

## Settings 
UM Settings -> General -> Uploads
1. PDF file meta_key - Enter the upload PDF file meta_key name.
2. Image file meta_key - Enter the upload PDF file converted Image file meta_key name.
3. Resolution - Enter the image conversion resolution. The higher integer value better resolution but also longer processing time for the conversion. Default value 400.
4. Converted Image width - Enter the Converted Image width in pixels for image full page view. Default value 1000.
5. Converted Image type - Select the Converted Image type. Options JPG, PNG, WEBP. Best compression with a WEBP image.

## Design
1. Max one uploaded PDF file per Registration or Profile Form for Image conversion.
2. First PDF page is converted to an Image.
3. Conversion will increase elapsed time based on resolution value during Registration/Profile save page.
4. PDF file delete during Profile page edit will also delete the Image.
5. Make the Image field non-editable for the user and the PDF file field only viewed by Admin and file owner.
6. Image is saved with the User's profile images and the file name format is like stream_photo_84c78013_372d28231f3f75a84e7996bcdd5c382448055576.pdf.webp with the hash value shared with the PDF file.

## Installation
1. Install by downloading the plugin ZIP file and install as a new Plugin, which you upload in WordPress -> Plugins -> Add New -> Upload Plugin.
2. Activate the Plugin: Ultimate Member - PDF convert Image
