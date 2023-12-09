# Nextcloud TV Show Namer

TV Show Namer for ownCloud and Nextcloud. Scan video files stored in your personal cloud and organise them into a standard format. TV Show Namer uses TVDB ([thetvdb.com](https://thetvdb.com/)) and TMDB ([themoviedb.org](https://www.themoviedb.org/)) to rename your files.

### currently in development please report issues and suggestions

## Important updates
 - v1.0.0 
    - added TVDB ([thetvdb.com](https://thetvdb.com/)) data source
    - API Keys are no longer required by you. You can use a personal TMDB API Key by adding it in the data source section
 - v0.4.2
    - Support for years in the show name. Add the year at the end of then name e.g. 'Spitting Image 2020' or 'Spitting Image - 2020'
 - v0.4.0 
    - the way settings are stored has changed. Some users may need to re enter there API Key


## Try it

Download it form the Apps section in Nextcloud 

To install it change into your Nextcloud apps directory:

    cd nextcloud/apps

Then run or extract the release zip into the folder:

    git clone https://github.com/j4ym0/nextcloud-tv-namer tvshownamer

Next enable the app in the apps section in Nextcloud

The app should run on standard php installation


## Getting a personal API Key for TMDB
This product uses the TMDB API but is not endorsed or certified by TMDB.

As of v1.0.0 you do not need your own API Key to use the TMDB data source. If you would like to use your own API Key, enter it in data source.

 - Sign up for an account with [themoviedb.org](https://www.themoviedb.org/signup) at https://www.themoviedb.org/signup
 - [Click here for api page](https://www.themoviedb.org/settings/api) or
     - Click on your avatar or initials in the main navigation
     - Click the "Settings" link
     - Click the "API" link in the left sidebar
 - Click "Create" or "click here" on the API page
 - Now copy the API Key (v3 auth)
 - Past into the TV Show Namer app settings menu on the bottom left


## Naming Guide

When choosing your naming structure you can mix the below variables, letters and symbols. To use a variable they need to be wrapped in {{}}, e.g. {{Season_Name}}, the variables are not case sensitive but anything outside is case sensitive. Variables are pretty self explanatory but referenced below.

  - {{Season_Name}} - The season name as from TMDB
  - {{Series_Name}} - Same as above
  - {{Season_Year}} - The year the season aired from TMDB (e.g. 1954 or 2022)
  - {{Series_Year}} - Same as above
  - {{Season_Number}} - The season number of the episode
  - {{Series_Number}} - Same as above
  - {{Season_Number_Padded}} - Same as above but Season '1' would be '01' and Season '10' would be '10'
  - {{Series_Number_Padded}} - Same as above
  - {{Episode_Number}} - The episode number of the episode
  - {{Episode_Number_Padded}} - Same as above but Episode '1' would be '01' and Episode '10' would be '10'
  - {{Episode_Name}} - The episode name from TMDB

NOTE: Any incompatible symbols will be filleted out when renaming the file.


## Notes

If you add a '#' to the season folder name, everything after the '#' will be discounted when searching the data sources. This is useful if there are 2 programs with the same name. e.g. Spitting image and Spitting image 2020, Both are listed as 'Spitting image'. So 'Spitting image 2020' can become 'Spitting image #2020'


## TODO

 - Connect to DB
 - Cache API results
 - save poster to folders
 - better file recognition
 - recent scanned folders

