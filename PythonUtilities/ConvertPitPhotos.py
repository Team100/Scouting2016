

import glob, os
from PIL import Image
import json

size = 192,192
med_size = 180, 180
sm_size = 50, 50
max_size = 1200, 1200
max_width = 1200

tablets = ["Red1", "Red2", "Red3", "Blue1", "Blue2", "Blue3", "spare"]
basePath = "C:\\TabletFiles\\"
rawDataPath = os.path.join(basePath, "testfiles")
pictureStoragePath = os.path.join(basePath, "PhotosForServer")
if not os.path.isdir(pictureStoragePath):
    os.mkdir(pictureStoragePath)


def parse_pit_json_files():
    teamList = dict()
    for tablet in tablets:
        pitDataPath = os.path.join(os.path.join(rawDataPath, tablet),"Scouting\\Pit") 
        pictureDataPath = os.path.join(os.path.join(rawDataPath, tablet),"Pictures") 
        if os.path.isdir(pitDataPath):
            print pitDataPath
            for pitfile in glob.glob(os.path.join(pitDataPath,"*.json")):
                with open(pitfile) as jsonfile:
                    fileContents = jsonfile.read()
                    try:
                        team = json.loads(fileContents)
                    except:
                        print "Problem with JSON file: ", pitfile
                    else:
                        print team
                        teamNum = team['Team Number']
                        pictureList = team['PictureList']
                        primaryPicture = os.path.join(pictureDataPath, team['Primary Photo'])
                        make_small(teamNum, primaryPicture, pictureStoragePath)
                        make_medium(teamNum, primaryPicture, pictureStoragePath)
                        save_all_photos(teamNum, pictureList, pictureDataPath, pictureStoragePath)
                        
def crop_resize_save(size, infile, outfile): 
    from PIL import Image as PILImage   
    im = PILImage.open(infile)
    w,h = im.size
    if w > h:
        #crop extra width to make square
        chop = (w - h) / 2
        box = (chop, 0, w - chop, h)
    else:
        #crop extra height to make square
        chop = (h - w) / 2
        box = (0, chop, w, h - chop)
        print box
    croppedImage = im.crop(box)       
    croppedImage.thumbnail(size, PILImage.ANTIALIAS)   
    croppedImage.save(outfile)             
                   
def make_small (teamId, inPhotofile, outPhotoPath):
    smallFile = os.path.join(outPhotoPath, "".join(["team-", str(teamId), "-small.jpg"]))
    crop_resize_save(sm_size, inPhotofile, smallFile)
    print "Saving: ", smallFile

def make_medium (teamId, inPhotofile, outPhotoPath):
    medFile = os.path.join(outPhotoPath, "".join(["team-", str(teamId), "-med.jpg"]))
    crop_resize_save(med_size, inPhotofile, medFile)
    print "Saving: ", medFile        

def save_all_photos(teamId, inPhotos, inPhotoPath, outPhotoPath):
    from PIL import Image as PILImage
    i = 1
    for photo in inPhotos:           
        infilename = os.path.join(inPhotoPath, photo)
        im = PILImage.open(infilename)
        w,h = im.size
        if w > max_width:
            im.thumbnail(max_size, PILImage.ANTIALIAS)              
        outfilename = os.path.join(outPhotoPath, "".join(["team-", str(teamId), "-", str(i), ".jpg"]))
        im.save(outfilename)
        i += 1
        
parse_pit_json_files()