import { Component, OnInit } from '@angular/core';
import { DivCount } from "../../DivCount"
import { DashboardService } from "../../services/dashboard.service"

@Component({
  selector: 'app-dashboard',
  templateUrl: './dashboard.component.html',
  styleUrls: ['./dashboard.component.css']
})
export class DashboardComponent implements OnInit {
	divCounts: DivCount[] = []

  constructor(private dashboardService: DashboardService) { }

  ngOnInit(): void {
	this.dashboardService
		.getDivCounts()
		.subscribe((divCounts) => this.divCounts = divCounts);
  }
}
