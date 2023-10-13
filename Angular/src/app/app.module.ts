import { NgModule } from '@angular/core';
import { BrowserModule } from '@angular/platform-browser';
import { HttpClientModule } from '@angular/common/http';

import { AppRoutingModule } from './app-routing.module';
import { AppComponent } from './app.component';
import { DashboardComponent } from './pages/dashboard/dashboard.component';
import { TopnavComponent } from './components/topnav/topnav.component';
import { DoughnutComponent } from './components/charts/doughnut/doughnut.component';
import { LineComponent } from './components/charts/line/line.component';
import { BarComponent } from './components/charts/bar/bar.component';

@NgModule({
  declarations: [
    AppComponent,
    DashboardComponent,
    TopnavComponent,
    DoughnutComponent,
    LineComponent,
    BarComponent
  ],
  imports: [
    BrowserModule,
    AppRoutingModule,
	HttpClientModule,
  ],
  providers: [],
  bootstrap: [AppComponent]
})
export class AppModule { }
