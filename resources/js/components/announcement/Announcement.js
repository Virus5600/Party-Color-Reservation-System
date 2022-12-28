import React, { useEffect, useReducer } from 'react';

// React-Bootstrap
import Container from 'react-bootstrap/Container';
import Row from 'react-bootstrap/Row';
import Col from 'react-bootstrap/Col';

// Styling with css
import styles from './Announcement.module.css';

// Image components
import foodImg from './img/food.png';

// Similar to fetch API
import axios from 'axios';

// To simulate data fetch
const initialAnnouncements = [
	{img: foodImg, date: '2022.1.5', title: 'Halloween 15% Discount Promo', description: 'BBQ & Drinks Plan Adult・senior high: ￥3,500 to ￥ 2,975 BBQ & Drinks Plan Adult・senior high: ￥3,500 to ￥ 2,975 BBQ & Drinks Plan Adult・senior high: ￥3,500 to ￥ 2,975'},
];

const announcementReducer = (currentState, action) => {
	switch (action.type) {
		case 'ANNOUNCEMENT_FETCH_SUCCESS':
			return {
				data: action.payload,
			};
		default:
			throw new Error();
	}
};

// To simulate data fetch
const getAsyncData = () =>
	new Promise(resolve => {
		setTimeout(() => resolve({ data: { announcements: initialAnnouncements }})
		, 2000);
	});

const API_ENDPOINT = `api/user/fetch-announcements`;																																																																																																																																																																																																		       			               							 

const Announcement = () => {
	// State
	const [announcements, dispatchAnnouncements] = useReducer(announcementReducer, { data: [] });

	// Old useEffect that throws an error (Retained just in case. Feel free to remove if it's alright)
	// useEffect(async () => {
	// 	try {
	// 		const result = await axios.get(API_ENDPOINT);
	// 		dispatchAnnouncements({
	// 			type: 'ANNOUNCEMENT_FETCH_SUCCESS',
	// 			payload: result.data
	// 		});
	// 	} catch {
	// 		throw new Error();
	// 	}
	// 	getAsyncData()
	// 		.then(result => dispatchAnnouncements({
	// 			type: 'ANNOUNCEMENT_FETCH_SUCCESS',
	// 			payload: result.data.announcements,
	// 		}))
	// 		.catch(() => new Error());
	// 	}, []
	// );
	
	// It is similar to componentDidMount and componentDidUpdate
	useEffect(() => {
		async function fetchAnnouncement() {
			try {
				const result = await axios.get(API_ENDPOINT);

				dispatchAnnouncements({
					type: 'ANNOUNCEMENT_FETCH_SUCCESS',
					payload: result.data
				});
			} catch (e) {
				throw new Error(e);
			}
			
			getAsyncData().then(
				(result) => {
					console.log(result);

					dispatchAnnouncements({
						type: 'ANNOUNCEMENT_FETCH_SUCCESS',
						payload: result.data.announcements,
					}
				)}
			).catch(
				() => new Error()
			);
		}

		fetchAnnouncement();
	}, []);

	console.log(announcements);
	return (
		<>
			<div className={styles.AnnouncementTitle}>
				<h1>ANNOUNCEMENT</h1>
			</div>
			<div className={styles.AnnouncementList}>
				{/*{ announcements.data.map(item => <AnnouncementItem {...item} />) }*/}
			</div>
		</>
	);
};

const AnnouncementItem = ({ img, date, title, description }) => (
	<Container fluid className={styles.AnnouncementItem} >
		<Row>
			<Col>
				<div className={styles.image}>
					<img src={img} alt='food' />
				</div>
			</Col>
			<Col>
				<span>{date}</span>
				<h2>{title}</h2>
				<p>{description}</p>
			</Col>
		</Row>
	</Container>
);

export default Announcement;