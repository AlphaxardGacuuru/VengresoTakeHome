import { Component, OnInit, Input } from "@angular/core"
import { Chart, registerables } from "chart.js"

@Component({
	selector: "app-doughnut",
	templateUrl: "./doughnut.component.html",
	styleUrls: ["./doughnut.component.css"],
})
export class DoughnutComponent implements OnInit {
	constructor() {
		Chart.register(...registerables)
	}

	@Input() title: string = "Doughnut Chart"
	@Input() labels: any
	@Input() data: any

	ngOnInit(): void {
		// Doughnut Chart
		const doughnutCanvasEle: any = document.getElementById("doughnut-chart")
		const doughnutChar = new Chart(doughnutCanvasEle.getContext("2d"), {
			type: "doughnut",
			options: { cutout: "90%", radius: "100%" },
			data: {
				labels: this.labels,
				datasets: [
					{
						data: this.data,
						backgroundColor: "#FFD700",
						borderColor: "#FFF",
						borderWidth: 1,
						hoverOffset: 4,
					},
				],
			},
		})
	}
}
