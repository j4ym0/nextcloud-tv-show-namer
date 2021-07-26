# Nextcloud TV Show Namer

TV Show Namer for ownCloud and Nextcloud. Scan video files stored in your cloud and organise them into a standard format. TV Show Namer uses themoviedb.org to rename your files, you can setup a api key with the guide below.

### currently in development please report issues and suggestions


## Try it

To install it change into your Nextcloud's apps directory:

    cd nextcloud/apps

Then run or extract the release zip into the folder:

    git clone https://github.com/j4ym0/nextcloud-tv-namer tvshownamer

Next enable the app in the apps section in nextcloud

The app should run on standard php installation, you will need a api key from themoviedb.org

## Getting your API Key

 - Signup for an account with [themoviedb.org](https://www.themoviedb.org/signup) at https://www.themoviedb.org/signup
 - [Click here for api page](https://www.themoviedb.org/settings/api) or
 - Click on your avatar or initials in the main navigation
 - Click the "Settings" link
 - Click the "API" link in the left sidebar
 - Click "Create" or "click here" on the API page
 - Now copy the API Key (v3 auth)
 - Past into the TV Show Namer app settings menu on the bottom left


## TODO

 - Connect to DB
 - Cache API results
 - save poster to folders
 - select all and confirm rename
 - better file recognition
 - recent scanned folders
