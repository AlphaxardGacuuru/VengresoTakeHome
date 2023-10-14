///<reference types="chrome"/>
import { Component, OnInit, NgZone } from "@angular/core"
import { DivCount } from "../../DivCount"
import { DashboardService } from "../../services/dashboard.service"
import Echo from "../../lib/echo"

@Component({
	selector: "app-dashboard",
	templateUrl: "./dashboard.component.html",
	styleUrls: ["./dashboard.component.css"],
})
export class DashboardComponent implements OnInit {
	divCounts: DivCount[] = []
	uniqueUrls = []
	groupedByMonth = []
	groupedByDivsPerUrl = []
	message: string

	constructor(
		private dashboardService: DashboardService,
		private ngZone: NgZone
	) {
		Echo.private("div-count-saved").listen("DivCountSavedEvent", (data) => {
			console.log("Received data:", data)
			this.ngZone.run(() => {
				this.getDivCounts()
			})
		})
	}

	ngOnInit(): void {
		// Get Dashboard Data
		this.dashboardService.getDashboardData().subscribe((res) => {
			this.uniqueUrls.push(res.uniqueUrls)

			// Collect Grouped By Month
			var groupedByMonthLabels = res.groupedByMonth.map((item) => item.month)
			var groupedByMonthData = res.groupedByMonth.map((item) => item.count)
			this.groupedByMonth = [groupedByMonthLabels, groupedByMonthData]

			// Collect Grouped By Divs Per Url
			var groupedByDivsPerUrlLabels = res.groupedByDivsPerUrl.map(
				(item) => item.url
			)
			var groupedByDivsPerUrlData = res.groupedByDivsPerUrl.map(
				(item) => item.divs
			)
			this.groupedByDivsPerUrl = [
				groupedByDivsPerUrlLabels,
				groupedByDivsPerUrlData,
			]
		})

		// Get Div Counts
		this.getDivCounts()

		// Handle Chrome Extension message

		// Listen for messages
		// chrome.runtime.onMessage.addListener((message, sender, sendResponse) => {
			// console.log(message)
			// Handle the message here
		// })
	}

	setMessage(message) {
		this.message = message
	}

	getDivCounts() {
		this.dashboardService
			.getDivCounts()
			.subscribe((res) => (this.divCounts = res.data))
	}
}
