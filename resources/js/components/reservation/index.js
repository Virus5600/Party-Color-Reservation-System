import React, { useState, useReducer } from 'react';

import axios from 'axios';

// css
import './style.css';



// icons
import { FontAwesomeIcon } from '@fortawesome/react-fontawesome';
import {
	faPerson,
	faEnvelope,
	faPhone,
	faCalendar,
	faClock,
	faCircleCheck,
	faClipboard,
	faCircleXmark,
}
	from '@fortawesome/free-solid-svg-icons';




// root
const Reservation = () => {

	function changeDateFormatToCompatible(current_date) {
		let year = current_date.getFullYear();
		let month = current_date.getMonth() + 1;
		if (month < 10) month = '0' + month.toString();
		let date = current_date.getDate();
		if (Number(date) < 10) date = '0' + date;
		let compatibleDateFormat = year + '-' + month + '-' + date;
		return compatibleDateFormat;
	};

	const reservationReducer = (state, action) => {
		switch (action.type) {
			case 'SET_FIRST_DETAIL':
				return {
					...state,
					pax: action.payload.pax,
					targetDate: action.payload.targetDate,
					targetTime: action.payload.targetTime,
				}
			case 'SET_SECOND_DETAIL':
				return {
					...state,
					fullName: action.payload.fullName,
					email: action.payload.email,
					phoneNumber: action.payload.phoneNumber,
					isCheckedUpdate: action.payload.isCheckedUpdate,
					specialRequest: action.payload.specialRequest,
					isSuccess: true, // dummy
				};
			case 'SUCCESS_SEND_RESERVATION':
				return {
					...state,
					isSuccess: true,
				}
			default: throw new Error();
		}
	};

	const [reservation, dispatchReservation] = useReducer(
		reservationReducer,
		{
			isSuccess: false,
			pax: 1,
			targetDate: changeDateFormatToCompatible(new Date()),
			targetTime: '',
			fullName: '',
			email: '',
			phoneNumber: '',
			isCheckedUpdate: false,
			specialRequest: '',
		}
	);




	return (
		<div className='Reservation'>
			{reservation.isSuccess ? <ReservationSuccess reservation={reservation} /> : <ReservationForm reservation={reservation} dispatchReservation={dispatchReservation} />}
		</div>
	);
};

// under development
const ReservationSuccess = ({ reservation }) => {
	return (
		<div className='ReservationConfirmed'>
			<div className='ReservationConfirmed-success'>

				<FontAwesomeIcon icon={faCircleCheck} className='ReservationConfirmed-success-icon' />

				<div>
					<span className='ReservationConfirmed-success-title'>Your reservation has been confirmed!</span><br />
					<span>An email confirmation has been sent to you.</span><br />
					<span>Confirmation #<span>001</span></span>
				</div>
			</div>
			<div className='ReservationConfirmed-details'>
				<div className='ReservationConfirmed-details-content'>

					<div className='ReservationConfirmed-details-left'>
						<DetailsRight date={reservation.targetDate} time={reservation.targetTime} number={reservation.pax} />
					</div>

					{/* <div className='ReservationConfirmed-details-right'>
						<div className='ReservationConfirmed-details-right-button'>
							<FontAwesomeIcon icon={faClipboard} className='ReservationConfirmed-details-right-icon' />
							<span>Modify</span>
						</div>
						<div className='ReservationConfirmed-details-right-button'>
							<FontAwesomeIcon icon={faCircleXmark} className='ReservationConfirmed-details-right-icon' />
							<span>Cancel</span>
						</div>
					</div> */}

				</div>


			</div>
		</div>
	);
};



const ReservationForm = ({ reservation, dispatchReservation }) => {

	const [isTimeClicked, setTimeClicked] = useState(false);

	const selectedStyle = { color: 'red' };
	const unselectedStyle = { color: 'black' };

	const handleTimeClick = () => {
		setTimeClicked(true);
	};

	return (
		<div>
			<div className='Reservation-form'>
				<h2>Reservation at Party Color</h2>
				<div className='Reservation-details'>
					<ol className='d-flex'>
						<li style={!isTimeClicked ? selectedStyle : unselectedStyle}>Select a Date</li>
						<li style={isTimeClicked ? selectedStyle : unselectedStyle}>Additional details</li>
					</ol>
					<hr />
					{!isTimeClicked ? <Dates reservation={reservation} dispatchReservation={dispatchReservation} onTimeClicked={handleTimeClick} /> : <Details reservation={reservation} dispatchReservation={dispatchReservation} />}

				</div>

			</div>
		</div>

	);

};

const Dates = ({ reservation, dispatchReservation, onTimeClicked }) => {
	const [pax, setPax] = useState(1);
	const [targetDate, setTargetDate] = useState(convertDateFormat(new Date()));
	const [isTimeAvailable, setTimeAvailable] = useState(false);

	const handlePax = (event) => {
		setPax(event.target.value);
	};
	const handleClickDate = (event) => {
		setTargetDate(convertDateFormat(new Date(event.target.value)));
	};

	const handleClickFindTable = () => {
		setTimeAvailable(true);
	};

	const handleTimeClick = (targetTime) => {
		dispatchReservation({
			type: 'SET_FIRST_DETAIL',
			payload: {
				pax: pax,
				targetDate: targetDate,
				targetTime: targetTime,
			},
		});
		onTimeClicked(true);
	}

	function convertDateFormat(current_date) {
		let year = current_date.getFullYear();
		let month = current_date.getMonth() + 1;
		if (month < 10) month = '0' + month.toString();
		let date = current_date.getDate();
		if (Number(date) < 10) date = '0' + date;
		let compatibleDateFormat = year + '-' + month + '-' + date;
		return compatibleDateFormat;
	};

	// temporary data


	return (
		<div className='Dates'>
			<input type='number' min='1' value={pax} onChange={handlePax} />
			<input type='date' min={targetDate} value={targetDate} onChange={handleClickDate} />
			<button className='find-table-button' onClick={handleClickFindTable}>Find a table</button>

			{isTimeAvailable ? <TimeList reservation={reservation} dispatchReservation={dispatchReservation} onTimeClicked={handleTimeClick} /> : null}

		</div>
	);
};

const TimeList = ({ onTimeClicked }) => {
	const times = [
		{ id: 1, time: '5:00PM' },
		{ id: 2, time: '6:00PM' },
		{ id: 3, time: '7:00PM' },
		{ id: 4, time: '8:00PM' },
	];
	return (
		<div className='TimeList'>
			{times.map(time => <Time key={time.id} time={time.time} onTimeClicked={onTimeClicked} />)}
		</div>
	);
};

const Time = ({ time, onTimeClicked }) => {

	const handleClickTime = () => {
		onTimeClicked(time);
	};

	return (
		<div className='Time'>
			<button onClick={handleClickTime}>{time}</button>
		</div>
	);

};



const Details = ({ reservation, dispatchReservation }) => {
	const [fullName, setFullName] = useState('');
	const [email, setEmail] = useState('');
	const [phoneNumber, setPhoneNumber] = useState('');
	const [isCheckedUpdate, setCheckedUpdate] = useState(false);
	const [specialRequest, setSpecialRequest] = useState('');
	const token = document.querySelector('meta[name=csrf-token]').content;

	// const API_TO_SEND_RESERVATION = 'api/react/reservations/create';

	// const handleClickServeButton = async () => {
	// 	let flag;
	// 	const result = await axios.post(API_TO_SEND_RESERVATION, {
	// 		_token: token,						// _token [string] - For CSRF prevention
	// 		reservation_date: date,
	// 		pax: Number(number),
	// 		price: 3000,  // static as of now
	// 		time_hour: Number(time.split(':')[0]) + 12,
	// 		time_min: 0,  // static as of now
	// 		reservation_time: Number(time.split(':')[0]),
	// 		extension: 0, // static as of now
	// 		menu: [1], // static as of now since plan has only one
	// 		contact_name: [fullName],
	// 		contact_email: [email],
	// 		phone_numbers: phoneNumber,
	// 		specialRequest: specialRequest,		// Not yet implemented (backend)
	// 		subscribed: isCheckedUpdate,		// Not yet implemented (backend)
	// 	}).then(response => {
	// 		if (response.data.success) flag = true;
	// 		if (response.success) {
	// 			Swal.fire({
	// 				title: response.flash_success,
	// 				position: `top`,
	// 				showConfirmButton: false,
	// 				toast: true,
	// 				timer: 10000,
	// 				background: `#28a745`,
	// 				customClass: {
	// 					title: `text-white`,
	// 					content: `text-white`,
	// 					popup: `px-3`
	// 				},
	// 			});
	// 			console.log(response);

	// 		}
	// 		else {
	// 			if (response.type == 'error') {
	// 				Swal.fire({
	// 					title: response.flash_error,
	// 					position: `top`,
	// 					showConfirmButton: false,
	// 					toast: true,
	// 					timer: 10000,
	// 					background: `#28a745`,
	// 					customClass: {
	// 						title: `text-white`,
	// 						content: `text-white`,
	// 						popup: `px-3`
	// 					},
	// 				})
	// 				console.log(response);

	// 			}
	// 			else if (response.type == 'validation') {
	// 				// IF VALIDATION FAILED
	// 				console.log(response.errors);

	// 			}
	// 		}

	// 	});

	// 	if (flag) setSuccess(true);
	// };
	const handleReserveButton = () => {
		dispatchReservation({
			type: 'SET_SECOND_DETAIL',
			payload: {
				fullName: fullName,
				email: email,
				phoneNumber: phoneNumber,
				isCheckedUpdate: isCheckedUpdate,
				specialRequest: specialRequest,
			}
		})
	}

	return (
		<>
			<div className='Details'>
				<DetailsLeft
					onChangeFullName={setFullName}
					onChangeEmail={setEmail}
					onChangePhoneNumber={setPhoneNumber}
					onCheckUpdate={setCheckedUpdate}
				/>
				<DetailsRight number={reservation.pax} date={reservation.targetDate} time={reservation.targetTime} specialRequest={true} onChangeSpecialRequest={setSpecialRequest} />
			</div>
			<button className='Details-reserve-button' onClick={handleReserveButton}>RESERVE</button>
		</>
	);
};

function addTwoHours(time) {
	const selectedTime = Number(time[0]);
	return (selectedTime + 2) + time.substring(1);
};

const DetailsRight = ({ number, date, time, ...others }) => {

	const handleChangeSpecialRequest = (event) => {
		others.onChangeSpecialRequest(event.target.value);
	};

	return (
		<div className='Details-right'>
			<h2>Party Color</h2>

			<div className='Details-right-item'>
				<FontAwesomeIcon className='Details-right-icon' icon={faCalendar} />
				<span>{date}</span>
			</div>

			<div className='Details-right-item'>
				<FontAwesomeIcon className='Details-right-icon' icon={faClock} />
				<span>{time} - {addTwoHours(time)}</span>
			</div>

			<div className='Details-right-item'>
				<FontAwesomeIcon className='Details-right-icon' icon={faPerson} />
				<span>{number} people</span>
			</div>


			{others.specialRequest ?
				<div>
					<hr />
					<input type='text' placeholder='Add special request(optional)' onChange={handleChangeSpecialRequest} />
				</div>
				:
				null
			}


		</div>
	);
};

const DetailsLeft = ({
	onChangeFullName,
	onChangeEmail,
	onChangePhoneNumber,
	onCheckUpdate
}) => {
	const handleChangeFullName = (event) => {
		onChangeFullName(event.target.value);
	};
	const handleChangeEmail = (event) => {
		onChangeEmail(event.target.value);
	};
	const handleChangePhoneNumber = (event) => {
		onChangePhoneNumber(event.target.value);
	};
	const handleCheckUpdate = () => {
		onCheckUpdate(true);
	};
	return (
		<div className='Details-left'>
			<div className='Details-left-input '>
				<FontAwesomeIcon className='Details-left-icon adjustment' icon={faPerson} />
				<input type='text' placeholder='Full name' onChange={handleChangeFullName} /><br />
			</div>

			<div className='Details-left-input'>
				<FontAwesomeIcon className='Details-left-icon' icon={faEnvelope} />
				<input type='email' placeholder='Email' onChange={handleChangeEmail} /><br />
			</div>
			<div className='Details-left-input'>
				<FontAwesomeIcon className='Details-left-icon' icon={faPhone} />
				<input type='text' placeholder='Phone number' onChange={handleChangePhoneNumber} /><br />
			</div>
			<div className='Details-left-input'>
				<input type="checkbox" id="update" name="update" onClick={handleCheckUpdate} />
				<label htmlFor="update">Yes, I want to get email and text updates about my reservation</label><br />
			</div>
		</div>
	);
};









export default Reservation;



// const [isReservationSuccess, setReservationSuccess] = useState(false);
// const [pax, setPax] = useState(1);
// const [targetDate, setTargetDate] = useState(changeDateFormatToCompatible(new Date()));
// const [targetTime, setTargetTime] = useState('');
// const [fullName, setFullName] = useState('');
// const [email, setEmail] = useState('');
// const [phoneNumber, setPhoneNumber] = useState('');
// const [isCheckedUpdate, setCheckedUpdate] = useState(false);
// const [specialRequest, setSpecialRequest] = useState('');

// const handlePax = (selected_pax) => {
// 	setPax(selected_pax);
// };

// const handleTargetDate = (selected_targetDate) => {
// 	setTargetDate(selected_targetDate);
// };

// const handleTargetTime = (selected_targetTime) => {
// 	setTargetTime(selected_targetTime);
// };

// const handleFullName = (fullName) => {
// 	setFullName(fullName);
// };

// const handleEmail = (email) => {
// 	setEmail(email);
// };

// const handlePhoneNumber = (phoneNumber) => {
// 	setPhoneNumber(phoneNumber);
// };

// const handleCheckedUpdate = (checkedUpdate) => {
// 	setCheckedUpdate(checkedUpdate);
// };

// const handleSpecialRequest = (specialRequest) => {
// 	setSpecialRequest(specialRequest);
// };