# Spotify Most Popular

## Installation

`make ci`

`cp .env.example .env`

Create a spotify app in [Spotify Developer Dashboard](https://developer.spotify.com/dashboard/applications) and update the environment variables. <br > 
```dotenv
SPOTIFY_CLIENT_ID=
SPOTIFY_CLIENT_SECRET=
SPOTIFY_REDIRECT_URI=
```

`sail up -d`

`sail npm install` <br />
`sail npm run build`

`sail artisan key:generate` <br />
`sail artisan migrate`

## Testing

`sail test`
