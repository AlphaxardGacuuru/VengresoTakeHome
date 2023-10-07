import React from "react"

import Messages from "@/components/Core/Messages"
import LoginPopUp from "@/components/Auth/LoginPopUp"

const AppLayout = ({ GLOBAL_STATE, children }) => {
	return (
		<>
			{/* Page Content */}
			<main>{children}</main>

			<Messages {...GLOBAL_STATE} />
			<LoginPopUp {...GLOBAL_STATE} />
		</>
	)
}

export default AppLayout
