import { Component, OnInit } from '@angular/core';

	const config = {
		type: "doughnut",
		options: { cutout: "90%", radius: "100%" },
		data: {
			labels: "props.labels",
			datasets: [
				{
					// label: "My First Dataset",
					data: "props.data",
					backgroundColor: "props.backgroundColor",
					borderColor: "#FFF",
					borderWidth: 1,
					hoverOffset: 4,
				},
			],
		},
	}

	const doughnutEl = document.getElementById("doughnut")

	
	@Component({
		selector: 'app-doughnut',
		templateUrl: './doughnut.component.html',
		styleUrls: ['./doughnut.component.css']
	})
export class DoughnutComponent implements OnInit {
	
	constructor() { }
	
	ngOnInit(): void {
	//   new Chart(doughnutEl, config)
  }

}
