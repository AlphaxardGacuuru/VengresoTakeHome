import { Component, OnInit, Input } from "@angular/core"
import { Chart, registerables } from "chart.js"

@Component({
	selector: "app-bar",
	templateUrl: "./bar.component.html",
	styleUrls: ["./bar.component.css"],
})
export class BarComponent implements OnInit {
	constructor() {
		Chart.register(...registerables)
	}

	@Input() title: string = "Bar Chart"
	@Input() labels: any
	@Input() data: any

	ngOnInit(): void {
		// Bar chart
		const barCanvasEle: any = document.getElementById("bar-chart")
		const barChart = new Chart(barCanvasEle.getContext("2d"), {
			type: "bar",
			data: {
				labels: this.labels,
				datasets: [
					{
						label: "Urls",
						data: this.data,
						backgroundColor: [
							"rgba(255, 99, 132, 0.2)",
							"rgba(255, 159, 64, 0.2)",
							"rgba(255, 205, 86, 0.2)",
							"rgba(75, 192, 192, 0.2)",
							"rgba(54, 162, 235, 0.2)",
							"rgba(153, 102, 255, 0.2)",
							"rgba(201, 203, 207, 0.2)",
						],
						borderColor: [
							"rgb(255, 99, 132)",
							"rgb(255, 159, 64)",
							"rgb(255, 205, 86)",
							"rgb(75, 192, 192)",
							"rgb(54, 162, 235)",
							"rgb(153, 102, 255)",
							"rgb(201, 203, 207)",
						],
						borderWidth: 1,
					},
				],
			},
			options: {
				responsive: true,
				scales: {
					y: {
						beginAtZero: true,
					},
				},
			},
		})
	}
}
