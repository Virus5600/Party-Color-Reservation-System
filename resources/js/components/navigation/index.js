// dependencies
import React, { useState } from 'react';
import parse from 'react-html-parser';

// components
import Reservation from '../reservation';
import AnnouncementContent from '../announcement/AnnouncementContent';

import logo from './img/logo.png';

import './style.css';


const Navigation = ({ children }) => {

	const [isReservationClicked, setReservationClicked] = useState(false);
	const [isAnnouncementClicked, setAnnouncementClicked] = useState(false);

	const [announcementContent, setAnnouncementContent] = useState({});

	const handleHomeClick = () => {
		setReservationClicked(false);
		setAnnouncementClicked(false);
	};

	const handleReservationClick = () => {
		setReservationClicked(true);
	};

	const handleAnnouncementClick = (title, summary, content) => {
		// announcementContent['title'] = title;
		// announcementContent['summary'] = summary;
		// announcementContent['content'] = content;
		setAnnouncementContent({
			'title': title,
			'summary': summary,
			'content': content,
		});
		setAnnouncementClicked(true);
	};


	const listStyle = {
		color: '#A52A2A',
		fontWeight: 800,
		fontSize: '25px',
		listStyle: 'none',
		display: 'inline',
		marginRight: '20px'
	};


	function convertObjectToHtml(object) {
		console.log(object);
		const htmlString = object.slice(0, object.length - 1);
		const reactElement = parse(htmlString);
		return reactElement;
	};


	return (
		<>
			<div className='Nav'>
				<div className='container-mb d-flex justify-content-between align-items-end'>
					<img src={logo} alt='logo' height='90' />
					<ul>
						<li style={listStyle} onClick={handleHomeClick}><a href='#'>Home</a></li>
						<li style={listStyle} onClick={handleReservationClick}><a href='#'>Reservation</a></li>
						<li style={listStyle}><a href='#Announcement'>Announcement</a></li>
						<li style={listStyle}><a href='#AboutUs'>About Us</a></li>
					</ul>
				</div>

			</div>
			{
				isReservationClicked ?
					<Reservation />
					:
					/*
						reference
						https://codeburst.io/a-complete-guide-to-props-children-in-react-c315fab74e7c#:~:text=children%20is%20a%20special%20prop,official%20documentation%20as%20%E2%80%9Cboxes%E2%80%9D.
					*/
					isAnnouncementClicked ?
						<AnnouncementContent
							title={announcementContent['title']}
							summary={announcementContent['summary']}
						>
							{convertObjectToHtml(announcementContent['content'])}
						</AnnouncementContent>

						:
						React.Children.map(children, (child) =>
							React.cloneElement(child, {
								onReservationClick: handleReservationClick, // attributes of each child
								onAnnouncementClick: handleAnnouncementClick
							})
						)
			}
		</>


	);

};


export default Navigation;