# Nextcloud TV Show Namer

TV Show Namer for ownCloud and Nextcloud. Scan video files stored in your cloud and organise them into a standard format. TV Show Namer uses themoviedb.org to rename your files, you can setup a api key with the guide below.

### currently in development please report issues and suggestions

### In v0.4.0 the way settings are stored has changed. Some users may need to re enter there API Key

## Try it

To install it change into your Nextcloud's apps directory:

    cd nextcloud/apps

Then run or extract the release zip into the folder:

    git clone https://github.com/j4ym0/nextcloud-tv-namer tvshownamer

Next enable the app in the apps section in nextcloud

The app should run on standard php installation, but you will need a api key from themoviedb.org. See [here](#getting-your-api-key) to get an api key.

## Getting your API Key

 - Signup for an account with [themoviedb.org](https://www.themoviedb.org/signup) at https://www.themoviedb.org/signup
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
  - {{Season_Number}} - The season number of the episode
  - {{Series_Number}} - Same as above
  - {{Season_Number_Padded}} - Same as above but 'Season 1' would be 'Season 01' and 'Season 10' would be 'Season 10'
  - {{Series_Number_Padded}} - Same as above
  - {{Episode_Number}} - The episode number of the episode
  - {{Episode_Number_Padded}} - Same as above but 'Episode 1' would be 'Episode 01' and 'Episode 10' would be 'Episode 10'
  - {{Episode_Name}} - The episode name from TMDB

NOTE: Any incompatible symbols will be filleted out when renaming the file.

## Notes

If you add a '#' to the season folder name, this will search themoviedb.org for everything before the '#'. This is particularly useful if there are 2 programs with the same name. e.g. Spitting image and Spitting image 2020, Both are listed as 'Spitting image'. So 'Spitting image 2020' can become 'Spitting image #2020'

## TODO

 - Connect to DB
 - Cache API results
 - save poster to folders
 - better file recognition
 - recent scanned folders
 - organisation wide api key
