/*global chrome*/

function countDivs() {
	const divCount = document.querySelectorAll("div").length
	return divCount
}

chrome.runtime.onMessage.addListener(function (request, sender, sendResponse) {
	if (request.action === "getDivCount") {
		const divCount = countDivs()
		sendResponse({
			url: window.location.href,
			count: divCount,
		})
	}
})

// Add Floating Button
const floatBtn = document.createElement("a")
floatBtn.classList.add("floatBtn")
floatBtn.textContent = countDivs()
document.body.appendChild(floatBtn)

// Add Style
const style = document.createElement("style")
style.innerHTML = `
      .floatBtn {
	position: fixed;
	bottom: 60px;
	right: 0px;
	z-index: 99;
	border: none;
	outline: none;
	cursor: pointer;
	border-top-left-radius: 50%;
	border-bottom-left-radius: 50%;
	box-shadow: 0 2px 6px 0 rgba(0, 0, 0, 0.3);
	font-size: 30px;
	height: 70px;
	text-align: center;
	width: 70px;
	line-height: 70px;
}

#floatBtn:hover {
	background-color: rgba(255, 255, 255, 0.3);
}

@media only screen and (min-width: 768px) and (max-width: 991px) {
	#floatBtn {
		bottom: 800px;
		right: 20px;
		height: 50px;
		width: 50px;
		line-height: 55px;
	}
}

@media only screen and (max-width: 767px) {
	#floatBtn {
		bottom: 80px;
		right: 20px;
		height: 50px;
		width: 50px;
		line-height: 55px;
	}
}
    `
document.head.appendChild(style)
