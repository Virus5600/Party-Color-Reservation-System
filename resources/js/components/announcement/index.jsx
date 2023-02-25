import React, { useState, useEffect } from 'react';
import axios from 'axios';

import './style.css';

// React Router
import { Link } from 'react-router-dom';



async function fetchAnnouncements() {
	// API url for fetching announcements
	const API_ENDPOINT = 'api/react/announcements/fetch';
	console.log('im here');
	let response = await axios.get(API_ENDPOINT);

	return response.data.announcements;
}





export default function Announcement() {

	const [announcements, setAnnouncements] = useState([]);

	useEffect(() => {

		async function getAnnouncements() {
			const data = await fetchAnnouncements();
			setAnnouncements(data);
		}

		getAnnouncements();
	}, []);

	return (
		<div className='Announcement p-4' id='Announcement'>
			<div className="container">
				<h1 className='text-center'>Announcement</h1>

				{
					announcements.length > 0 ?
						announcements.map(announcement =>
							<AnnouncementItem
								key={announcement.id}
								id={announcement.id}
								poster={announcement.poster}
								created_at={announcement.created_at}
								title={announcement.title}
								summary={announcement.summary}
							/>
						) : null
				}
			</div>
		</div>
	);
}

/**
 * -------------------------------------------------------
 * React memo hooks
 * -------------------------------------------------------
 * to render the component only when the props are changed
 * 
 * usage:
 * wrap the functional component with React.memo()
 * 
 * e.g. 
 * React.memo(<Component />);
 */
const AnnouncementItem = React.memo(({ id, poster, created_at, title, summary }) => {

	function changeDateFormat(string_date) {
		const current_date = new Date(string_date);

		let year = current_date.getFullYear();
		let month = current_date.getMonth() + 1;
		let date = current_date.getDate();

		if (month < 10)
			month = '0' + month.toString();

		if (Number(date) < 10)
			date = '0' + date;

		return year + '.' + month + '.' + date;
	};


	return (
		<Link to={`/announcements/${id}`}>
			<div className='row AnnouncementItem text-black mb-4'>
				<div className='col-md-5 AnnouncementItem-image d-flex align-items-center'>
					<div className='ratio ratio-16x9'>
						<img src={poster.replace("{id}", id)} className='' />
					</div>
				</div>

				<div className='col'>
					<span className='date fs-4'>{changeDateFormat(created_at)}</span><br />
					<span className='title fs-2 fw-bold'>{title}</span><br />
					<span className='summary fs-3'>{summary}</span>
				</div>
			</div>

		</Link>

	);
});