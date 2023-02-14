// dependencies
import React, { useEffect, useState } from 'react';

import logo from './img/logo.png';
import './style.css';

import Home from '../home';

// react route
import { Outlet, Link, useNavigate } from 'react-router-dom';


export default function Navigation() {
	const navigate = useNavigate();

	useEffect(() => {
		navigate('/home', { replace: true });
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
								<li style={listStyle} ><Link to='home'>Home</Link></li>
								<li style={listStyle} ><Link to='reservation'>Reservation</Link></li>
								<li style={listStyle}><Link to='announcement'>Announcement</Link></li>
								<li style={listStyle}><Link to='aboutus'>About Us</Link></li>
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