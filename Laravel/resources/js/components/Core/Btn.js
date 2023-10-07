import React from "react"

const Btn = ({ btnStyle, btnClass, btnText, onClick, loading, disabled }) => (
	<button
		style={btnStyle}
		className={`btn rounded-pill text-uppercase ${btnClass}`}
		onClick={onClick}
		disabled={disabled}>
		{btnText}
		{loading && (
			<div
				className="spinner-border my-auto"
				style={{ color: "inherit" }}></div>
		)}
	</button>
)

Btn.defaultProps = {
	btnClass: "btn-primary text-white",
	loading: false,
	disabled: false,
}

export default Btn
