import React, { useState } from "react"
import { useHistory, useLocation } from "react-router-dom"
import CryptoJS from "crypto-js"

import Btn from "@/components/Core/Btn"

import CloseSVG from "@/svgs/CloseSVG"


const LoginPopUp = (props) => {
	const history = useHistory()
	const location = useLocation()

	const [email, setEmail] = useState("johndoe@gmail.com")
	const [phone, setPhone] = useState("0700000000")
	const [loading, setLoading] = useState(false)
	
	// Encrypt Token
	const encryptedToken = (token) => {
		const secretKey = "VengresoAuthorizationToken"
		// Encrypt
		return CryptoJS.AES.encrypt(token, secretKey).toString()
	}

	const onSubmit = (e) => {
		setLoading(true)
		e.preventDefault()

		Axios.get("/sanctum/csrf-cookie").then(() => {
			Axios.post(`/login`, {
				phone: phone,
				password: phone,
				device_name: "deviceName",
				remember: "checked",
			})
				.then((res) => {
					props.setMessages([res.data.message])
					// Remove loader
					setLoading(false)
					// Hide Login Pop Up
					props.setLogin(false)
					// Encrypt and Save Sanctum Token to Local Storage
					props.setLocalStorage("sanctumToken", encryptedToken(res.data.data))
					// Update Logged in user
					props.get(`auth`, props.setAuth, "auth", false)
					// Reload page
					setTimeout(() => window.location.reload(), 1000)
				})
				.catch((err) => {
					// Remove loader
					setLoading(false)
					props.getErrors(err)
				})

			// setPhone("07")
		})
	}

	const blur = props.auth.name == "Guest"

	return (
		<div className={blur ? "menu-open" : ""}>
			<div
				className="background-blur"
				style={{ visibility: blur ? "visible" : "hidden" }}></div>
			<div className="bottomMenu">
				<div className="d-flex align-items-center justify-content-between">
					{/* <!-- Logo Area --> */}
					<div className="logo-area p-2">
						<a href="#">Login</a>
					</div>
					{/* <!-- Close Icon --> */}
					<div
						className="closeIcon float-end"
						style={{ fontSize: "1em" }}
						onClick={() => {
							props.setLogin(false)
							// Check location to index
							history.push("/")
						}}>
						<CloseSVG />
					</div>
				</div>
				<div className="p-2">
					<center>
						<div className="mycontact-form">
							<form method="POST" action="" onSubmit={onSubmit}>
								<input
									id="email"
									type="text"
									className="form-control"
									name="email"
									value={email}
									onChange={(e) => setEmail(e.target.value)}
									required={true}
									autoFocus
								/>
								<br />

								<Btn type="submit" btnText="Login" loading={loading} />
							</form>
						</div>
					</center>
				</div>
			</div>
		</div>
	)
}

export default LoginPopUp
