import { Component, OnInit } from "@angular/core"
import { DivCount } from "../../DivCount"
import { DashboardService } from "../../services/dashboard.service"

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

	constructor(private dashboardService: DashboardService) {}

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
		this.dashboardService
			.getDivCounts()
			.subscribe((res) => (this.divCounts = res.data))
	}
}
