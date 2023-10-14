import { Injectable } from "@angular/core"
import { HttpClient, HttpHeaders } from "@angular/common/http"
import { Observable } from "rxjs"

import { DivCount } from "../DivCount"

const httpOptions = {
	headers: new HttpHeaders({
		"Content-Type": "application/json",
	}),
}

@Injectable({
	providedIn: "root",
})
export class DashboardService {
	constructor(private http: HttpClient) {}

	private apiUrl = "http://localhost:8000"

	onLogin(formData): Observable<any> {
		return this.http.post(`${this.apiUrl}/login`, formData, httpOptions)
	}

	getDashboardData(): Observable<any> {
		return this.http.get(`${this.apiUrl}/api/dashboard`)
	}

	getDivCounts(): Observable<any> {
		return this.http.get(`${this.apiUrl}/api/div-counts`)
	}
}
