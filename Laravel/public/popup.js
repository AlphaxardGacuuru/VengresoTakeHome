document.addEventListener("DOMContentLoaded", function () {
	document.getElementById("countDivs").addEventListener("click", function () {
		chrome.tabs.query({ active: true, currentWindow: true }, function (tabs) {
			chrome.tabs.sendMessage(
				tabs[0].id,
				{ action: "getDivCount" },
				function (response) {
					document.getElementById("divCount").innerText = response
						? response.count + " divs"
						: "Something went wrong, try reloading page"

					if (response) {
						sendDataToLaravel(response.count)
					}
				}
			)
		})
	})

	function sendDataToLaravel(divCount) {
		fetch("http://localhost:8000/api/div-counts", {
			method: "POST",
			headers: {
				"X-Requested-With": "XMLHttpRequest",
				"Content-Type": "application/json",
			},
			body: JSON.stringify({ divCount: divCount }),
		})
			.then((response) => response.json())
			.then((data) => {
				console.log("Data sent to dashboard:", data)
			})
			.catch((error) => {
				console.error("Error sending data:", error)
			})
	}
})
