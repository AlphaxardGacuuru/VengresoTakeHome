/*global chrome*/
/** @format */

// import logo from "./logo.svg"
import React, { useState } from "react"
import "./App.css"
import Axios from "./lib/Axios"

function App() {
	const [message, setMessage] = useState()
	const [error, setError] = useState()

	const onCountDivs = () => {
		chrome.tabs.query({ active: true, currentWindow: true }, function (tabs) {
			chrome.tabs.sendMessage(
				tabs[0].id,
				{ action: "getDivCount" },
				// Check if there's a response
				function (response) {
					if (response) {
						setMessage(`${response.count} divs`)
						sendDataToLaravel(response)
					} else {
						setError("Something went wrong, try reloading page")
					}
				}
			)
		})
	}

	// Send Counted Divs to Laravel
	const sendDataToLaravel = (res) => {
		let formData = new FormData()

		formData.append("url", res.url)
		formData.append("count", res.count)

		Axios.post("api/div-counts", formData)
			.then((res) => console.log(res))
			.catch((err) => console.log(err))
	}

	return (
		<React.Fragment>
			<div className="d-flex justify-content-center m-2">
				<button
					id="divCount"
					className="btn btn-lg btn-primary rounded-pill"
					onClick={onCountDivs}>
					Count Divs
				</button>
			</div>
			<div className="d-flex justify-content-center m-2">
				<div className="bg-success-subtle">{message}</div>
				<div className="bg-danger-subtle">{error}</div>
			</div>
		</React.Fragment>
	)
}

export default App
