import React from "react"

const Btn = ({ btnStyle, btnClass, btnText, onClick, loading, disabled }) => (
	<button
		style={btnStyle}
		className={btnClass}
		onClick={onClick}
		disabled={disabled}>
		{btnText}
		{loading && (
			<div
				className="spinner-border ms-2 my-auto"
				style={{ color: "inherit" }}></div>
		)}
	</button>
)

Btn.defaultProps = {
	btnClass: "sonar-btn white-btn",
	loading: false,
	disabled: false,
}

export default Btn
