import Echo from "laravel-echo"
import Pusher from "pusher-js"
import Axios from "axios"


// ;(window as any).Axios = require("axios")
// window.Axios.defaults.baseURL = process.env.MIX_APP_URL

Axios.defaults.headers.common["X-Requested-With"] =
	"XMLHttpRequest"

Axios.defaults.headers.common[
	"Authorization"
] = `Bearer ${JSON.parse(sessionStorage.getItem("sanctumToken"))}`

Axios.defaults.withCredentials = true

/**
 * Echo exposes an expressive API for subscribing to channels and listening
 * for events that are broadcast by Laravel. Echo and event broadcasting
 * allows your team to easily build robust real-time web applications.
 */

// window.Pusher = Pusher
;(window as any).Pusher = Pusher

const apiUrl: string = "http://localhost:8000/api/broadcasting/auth" 

const echo = new Echo({
	broadcaster: "pusher",
	key: 1,
	wsHost: window.location.hostname,
	wsPort: 6001,
	cluster: 1,
	forceTLS: false,
	disableStats: true,
	// authEndpoint: "localhost:8000/broadcasting/auth",
	authorizer: (channel, options) => {
		return {
			authorize: (socketId, callback) => {
				Axios.post(apiUrl, {
					socket_id: socketId,
					channel_name: channel.name,
				})
					.then((response) => callback(null, response.data))
					.catch((error) => callback(error))
			},
		}
	},
})

export default echo
