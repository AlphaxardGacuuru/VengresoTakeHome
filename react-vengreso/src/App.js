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
			<div
				className="d-flex justify-content-center m-2"
				style={{ minWidth: "20em" }}>
				<button
					id="divCount"
					className="btn btn-lg btn-primary rounded-pill"
					onClick={onCountDivs}>
					Count Divs
				</button>
			</div>
			{message || error ? <div className="d-flex flex-column justify-content-center w-50 mx-auto">
				<div className="bg-success-subtle rounded-pill p-2 text-center text-muted m-1">
					{message}
				</div>
				<div className="bg-danger-subtle rounded-pill p-2 text-center text-muted m-1">
					{error}
				</div>
			</div> : ""}
		</React.Fragment>
	)
}

export default App
