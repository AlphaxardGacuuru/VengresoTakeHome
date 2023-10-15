import { Component, OnInit, Output, EventEmitter } from "@angular/core"
import { HttpClient, HttpHeaders } from "@angular/common/http"
import { Observable } from "rxjs"

import { DashboardService } from "src/app/services/dashboard.service"

@Component({
	selector: "app-login",
	templateUrl: "./login.component.html",
	styleUrls: ["./login.component.css"],
})
export class LoginComponent implements OnInit {
	@Output() onMessage: EventEmitter<string> = new EventEmitter()

	constructor(private dashboardService: DashboardService) {}

	email: string = "johndoe@gmail.com"
	password: string = "0700000000"

	ngOnInit(): void {}

	isNotLoggedIn(): boolean {
		return sessionStorage.getItem("sanctumToken") ? false : true
	}

	onSubmit() {
		const formData = {
			email: this.email,
			phone: "0700000000",
			password: "0700000000",
			device_name: "deviceName",
		}

		this.dashboardService.onLogin(formData).subscribe((res) => {
			// Show message
			this.onMessage.emit(res.message)
			// Save token to local storage
			sessionStorage.setItem("sanctumToken", JSON.stringify(res.data))
			// Reload page for Websocket Connection
			setTimeout(() => window.location.reload(), 500)
		})
	}
}
