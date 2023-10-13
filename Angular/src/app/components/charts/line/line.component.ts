import { Component, OnInit, Input } from '@angular/core';
import { Chart, registerables } from "chart.js"

@Component({
	selector: "app-line",
	templateUrl: "./line.component.html",
	styleUrls: ["./line.component.css"],
})
export class LineComponent implements OnInit {
	constructor() {
		Chart.register(...registerables)
	}

	@Input() title: string = "Line Chart"
	@Input() labels: any
	@Input() data: any

	ngOnInit(): void {
		// Line Chart
		const lineCanvasEle: any = document.getElementById("line-chart")
		const lineChar = new Chart(lineCanvasEle.getContext("2d"), {
			type: "line",
			data: {
				labels: this.labels,
				datasets: [
					{
						data: this.data,
						label: "Divs",
						borderColor: "rgba(54, 162, 235)",
					}
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
