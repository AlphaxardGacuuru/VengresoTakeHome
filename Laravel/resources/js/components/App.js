import React, { useState, useEffect, useRef } from "react"
import ReactDOM from "react-dom"
import { HashRouter, Switch } from "react-router-dom"
// import Axios from "axios"

import AppLayout from "./Layouts/AppLayout"

import { random } from "lodash"

function App() {
	// Function for checking local storage
	const getLocalStorage = (state) => {
		if (typeof window !== "undefined" && localStorage.getItem(state)) {
			return JSON.parse(localStorage.getItem(state))
		} else {
			return []
		}
	}

	// Function for checking local storage
	const getLocalStorageAuth = (state) => {
		if (typeof window !== "undefined" && localStorage.getItem(state)) {
			return JSON.parse(localStorage.getItem(state))
		} else {
			return {
				name: "Guest",
				avatar: "/storage/avatars/male-avatar.png",
				accountType: "normal",
				decos: 0,
				posts: 0,
				fans: 0,
			}
		}
	}

	// Function to set local storage
	const setLocalStorage = (state, data) => {
		localStorage.setItem(state, JSON.stringify(data))
	}

	const url = process.env.MIX_APP_URL

	// Declare states
	const [messages, setMessages] = useState([])
	const [errors, setErrors] = useState([])

	const [auth, setAuth] = useState(getLocalStorageAuth("auth"))
	const [login, setLogin] = useState()

	// Function for fetching data from API
	const get = (endpoint, setState, storage = null, errors = true) => {
		Axios.get(`/api/${endpoint}`)
			.then((res) => {
				var data = res.data ? res.data.data : []
				setState(data)
				storage && setLocalStorage(storage, data)
			})
			.catch(() => errors && setErrors([`Failed to fetch ${endpoint}`]))
	}

	// Function for getting errors from responses
	const getErrors = (err, message = false) => {
		const resErrors = err.response.data.errors
		var newError = []
		for (var resError in resErrors) {
			newError.push(resErrors[resError])
		}
		// Get other errors
		message && newError.push(err.response.data.message)
		setErrors(newError)
	}

	useEffect(() => {
		get("auth", setAuth, "auth", false)
	}, [])

	console.log("rendered")

	const GLOBAL_STATE = {
		url,
		getLocalStorage,
		setLocalStorage,
		messages,
		setMessages,
		errors,
		setErrors,
		get,
		getErrors,
		auth,
		setAuth,
		login,
		setLogin,
	}

	return (
		<HashRouter>
			<AppLayout GLOBAL_STATE={GLOBAL_STATE}>
				<h1 className="w-25 mt-5 mx-auto">Vengreso Extension</h1>
			</AppLayout>
		</HashRouter>
	)
}

export default App

if (document.getElementById("app")) {
	ReactDOM.render(<App />, document.getElementById("app"))
}
