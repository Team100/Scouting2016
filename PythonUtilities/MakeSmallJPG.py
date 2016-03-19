

import glob, os
from PIL import Image

size = 192,192

def resize_them():
    from PIL import Image as PILImage
    for infile in glob.glob("TeamPhotos/*.jpg"):
        file, ext = os.path.splitext(infile)
        head, tail = os.path.split (file)
        im = PILImage.open(infile)
        im.thumbnail(size, PILImage.ANTIALIAS)
        file = os.path.join ("images", tail)
        if not os.path.isdir("images"):
            os.mkdir ("images")
        im.save(file + ".jpg")
resize_them()
