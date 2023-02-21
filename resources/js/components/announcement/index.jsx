import React, { useState, useEffect } from 'react';
import axios from 'axios';

import './style.css';

// React Router
import { useLoaderData, Link } from 'react-router-dom';

export async function loaderLatest() {
	if (sessionStorage.getItem('latestannouncement') == null) {
		const API_ENDPOINT = 'api/react/announcements/fetch';
		const result = await axios.get(API_ENDPOINT);
		// console.log(result.data.announcements);
		const announcements = result.data.announcements.filter(announcement => announcement.id < 4);
		sessionStorage.setItem('latestannouncement', JSON.stringify(announcements));
		return announcements;
	}

	const latestannouncements = JSON.parse(sessionStorage.getItem('latestannouncement')).filter(announcement => announcement.id < 4);
	return latestannouncements;

}

export async function loader() {
	if (sessionStorage.getItem('announcement') == null) {
		const API_ENDPOINT = 'api/react/announcements/fetch';
		const result = await axios.get(API_ENDPOINT);
		// console.log(result.data.announcements);
		const announcements = result.data.announcements;
		sessionStorage.setItem('announcement', JSON.stringify(announcements));
		return announcements;
	}
	const announcements = JSON.parse(sessionStorage.getItem('announcement'));
	return announcements;

}



export default function Announcement() {
	const announcements = useLoaderData();

	return (
		<div className='Announcement' id='Announcement'>
			<h1>Announcement</h1>
			
			<div className='Announcement-list'>
				{
					announcements.map(announcement =>
						<AnnouncementItem
							key={announcement.id}
							id={announcement.id}
							poster={announcement.poster}
							created_at={announcement.created_at}
							title={announcement.title}
							summary={announcement.summary}
						/>
					)
				}
			</div>
		</div>
	);
}


const AnnouncementItem = ({ id, poster, created_at, title, summary }) => {
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
			<div className='AnnouncementItem'>
				<div className='AnnouncementItem-image'>
					<img src={poster.replace("{id}", id)} />
				</div>

				<div className='AnnouncementItem-description'>
					<span className='date'>{changeDateFormat(created_at)}</span><br />
					<span className='title'>{title}</span><br />
					<span className='summary'>{summary}</span>
				</div>
			</div>

		</Link>

	);
};