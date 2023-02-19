// dependencies
import React, { useEffect } from 'react';

import logo from './img/logo.png';
import './style.css';


// react route
import { Outlet, NavLink, useNavigate } from 'react-router-dom';


export default function Navigation() {
	const navigate = useNavigate();

	useEffect(() => {
		navigate(`/${loadedPage}`, { replace: true });
	}, []);

	const listStyle = {
		color: '#A52A2A',
		fontWeight: 800,
		fontSize: '25px',
		listStyle: 'none',
		display: 'inline',
		marginRight: '20px'
	};

	return (
		<>
			{/* Navbar start */}

			<div className='Nav'>
				<nav className='navbar navbar-expand-lg'>
					<div className="container">
						<img src={logo} alt='logo' height='90' />
						<button className="navbar-toggler collapsed"
							type="button"
							data-bs-toggle="collapse"
							data-bs-target="#navbarSupportedContent"
							aria-controls="navbarSupportedContent"
							aria-expanded="false"
							aria-label="Toggle navigation">
							<span className="navbar-toggler-icon"></span>
						</button>
						<div className="navbar-collapse collapse" id="navbarSupportedContent">
							<ul className="nav nav-pills ms-auto mb-2 mb-lg-0">
								{/* HOME */}
								<li style={listStyle}>
									<NavLink to='/' className={({ isActive, isPending }) => isActive ? 'nav-active' : ''}>
										Home
									</NavLink>
								</li>
								
								{/* RESERVATION */}
								<li style={listStyle} >
									<NavLink to='reservation' className={({ isActive, isPending }) => isActive ? 'nav-active' : ''}>
										Reservation
									</NavLink>
								</li>

								{/* RESERVATION */}
								<li style={listStyle}>
									<NavLink to='announcements' className={({ isActive, isPending }) => isActive ? 'nav-active' : ''}>
										Announcement
									</NavLink>
								</li>
								
								{/* ABOUT US */}
								<li style={listStyle}>
									<NavLink to='about-us' className={({ isActive, isPending }) => isActive ? 'nav-active' : ''}>
										About Us
									</NavLink>
								</li>
							</ul>
						</div>
					</div>
				</nav>
			</div>

			{/* Navbar end */}
			<Outlet />
		</>
	);
}