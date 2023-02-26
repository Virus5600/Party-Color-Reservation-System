import '../style.css';

import React from 'react';

import axios from 'axios';
import { useLoaderData, Form, Link } from 'react-router-dom';

import ReservationStatus from '../reservationsuccess';

export function loader() {
	const rawdata = sessionStorage.getItem('reservationInfo');
	return JSON.parse(rawdata);
}


export default function ReservationConfirmation(props) {

	// this is used for change the page to success page
	const [success_booking, set_success_booking] = React.useState(false);


	/**
	 * -----------------
	 * reservationInfo
	 * -----------------
	 * if props exist then set its reservationInfo
	 * otherwise use the data from session storage
	 */
	const reservationInfo = props.forViewReservation == true ? props.reservationInfo : useLoaderData();


	const {
		first_name,
		last_name,
		email,
		phone,
		adult_senior,
		junior,
		elementary,
		date,
		starting_time,
		extension,
		special_request,

	} = reservationInfo;

	const sendReserveRequest = async () => {

		/**
		  * flow
		  * 1. get the reservation info made in create reservation page
		  * 2. send the reservation info to backend
		  * 3. if success go to success page
		  * 4. if not success go back to create reservation page
		  */

		// 1.
		const raw_session_data = sessionStorage.getItem('reservationInfo');

		// 2.
		const isSuccess = await handleReserveClick(JSON.parse(raw_session_data));

		// 3. and 4.
		set_success_booking(isSuccess);
	}


	if (success_booking) {
		return <ReservationStatus
			title={'Your reservation has been confirmed!'}
			description={'An email confirmation has been sent to you.'}
			link_label={'make another reservation'}
			link={'/reservation'}
			bg_style={{ backgroundColor: '#1D7B3E' }}
			icon_style={{ color: '#00ff59a1' }}
		/>
	}
	return (
		<div className='container container-small'>
			<div className='background m-5'>
				<h1 className='text-center text-white'>Reservation Details</h1>

				<Form className='px-sm-5 p-4'>
					<div className="text-white">
						<FieldValue label={'Full Name'} data={first_name + ' ' + last_name} />
						<FieldValue label={'Email'} data={email} />
						<FieldValue label={'Phone'} data={phone} />
						<FieldValue label={'Adult/Senior'} data={adult_senior} />
						<FieldValue label={'Junior'} data={junior} />
						<FieldValue label={'Elementary'} data={elementary} />
						<FieldValue label={'Reservation'} data={date + ' ' + starting_time} />
						<FieldValue label={'Time Extension'} data={extension} />
						<FieldValue label={'Special Requests'} data={special_request} />
					</div>
					<div className='text-white text-center '>
						<span>total</span><br />
						<span className='fs-1 fw-bold'>¥{compute_price(Number(adult_senior), Number(junior), Number(elementary))}</span>
					</div>
					{
						props.forViewReservation ? <ReservationButtonsForView cancel_request_reason={props.reservationInfo.cancel_request_reason} isCancelled={props.reservationInfo.isCancelled} /> : <ReservationButtons onReserveClick={sendReserveRequest} />
					}
				</Form>
			</div>
		</div>
	);
}



// this is used in reservation confirm page
const ReservationButtons = (props) => {

	/**
	 * current solution
	 * 1. send the data in sessionStorage to backend
	 * 2. if success go to success page
	 * 3. if not go back to reservation make page
	 * technical process
	 *  submit -> action (save the key for accessing the success page) -> load success page
	 * 
	 * 
	 * new solution
	 * 1. use state for success page, cancel page, undo cancel page
	 * 2. pass the props to button
	 * 3. make onClick handler for success, cancel, undo cancel
	 */

	const handleReserveClick = () => {
		props.onReserveClick();
	}

	return (
		<div className='text-end mt-4'>
			<Link to='/reservation'><button className='btn btn-danger mx-2'>Edit</button></Link>
			<button className='btn btn-success mx-2' type='button' onClick={handleReserveClick}>Reserve</button>
		</div>
	);
}


// this is used in reservation view page
const ReservationButtonsForView = (props) => {


	const [textAreaContent, setTextAreaContent] = React.useState(props.cancel_request_reason);

	const handleTextAreaInput = (event) => {
		sessionStorage.setItem('cancellation_reason', event.target.value);
		setTextAreaContent(event.target.value);
	}

	const handleUndoButton = (event) => {
		alert('not implemented yet');
	}

	// console.log('ReservationButtonsForView mounted!!');
	// console.log('props.cancel_request_reason:', props.cancel_request_reason);
	// console.log(props.cancel_request_reason === null ? '' : props.cancel_request_reason);

	return (
		<>
			<div className='row mt-sm-4 my-3'>
				<label htmlFor="cancellation-reason" className='col-5 text-white form-label' style={{ fontWeight: '900' }}>Cancellation Reason:</label>
				<div className='col-12'>
					<textarea
						className="form-control"
						id="cancellation-reason"
						rows='4'
						value={textAreaContent}
						onChange={handleTextAreaInput}
						style={{ resize: 'none' }}
						required={props.isCancelled ? true : false}
						disabled={props.isCancelled ? true : false}
					>
					</textarea>
				</div>
			</div>

			{/* undo cancel request button */}
			<div className='text-end mt-4'>
				<button className='btn btn-secondary' type='button' onClick={handleUndoButton} disabled={!props.isCancelled ? true : false}>Undo Cancel Request</button>
			</div>

			{/* cancel request button */}
			<div className='text-end mt-4'>
				<button className='btn btn-danger' type='submit' onClick={handleUndoButton} disabled={props.isCancelled ? true : false}>Cancel Request</button>
			</div>
		</>

	);
}

const FieldValue = ({ label, data }) => {
	return (
		<div className='row mb-3 gx-sm-3'>
			<div className='col-5 text-end' style={{ fontWeight: '900' }}>
				{label}:
			</div>
			<div className='col'>
				{data == '' ? 'N/A' : data}
			</div>
		</div>
	);
}

const static_prices = {
	'adult': 3500,
	'junior': 2000,
	'elementary': 1000,
};

function compute_price(no_adult, no_junior, no_elementary) {
	return no_adult * static_prices['adult'] + no_junior * static_prices['junior'] + no_elementary * static_prices['elementary'];
}

const handleReserveClick = async ({
	first_name,
	last_name,
	email,
	phone,
	adult_senior,
	junior,
	elementary,
	date,
	starting_time,
	extension,
	special_request,
}) => {

	const token = document.querySelector('meta[name=csrf-token]').content;
	const API_TO_SEND_RESERVATION = '/api/react/bookings/create';


	let menu = [], amount = [], pax = 0, index = 0;
	for (let p of [Number(adult_senior), Number(junior), Number(elementary)]) {
		index++;

		if (p <= 0)
			continue;

		menu.push(index);
		amount.push(p);
		pax += Number(p);
	}

	const response = await axios.post(API_TO_SEND_RESERVATION, {
		_token: token,																	// Cross site forgery (security concepts) csrf attacks
		booking_date: date,
		booking_type: 'reservation',
		pax: pax,
		price: compute_price(Number(adult_senior), Number(junior), Number(elementary)),
		time_hour: Number(starting_time.split(':')[0]),
		time_min: Number(starting_time.split(':')[1]),
		booking_time: starting_time,
		extension: extension == null ? 0 : Number(extension),							// It can take in numbers divisible by 0.5
		menu: menu,																		// Static
		amount: amount,
		phone_numbers: phone,
		contact_name: [last_name + ' ' + first_name],
		contact_email: [email],
		special_request: special_request,
	}).then(response => {
		// Do something after send the data to backend
		console.log(response);

		const data = response.data;
		if (data.success) {
			sessionStorage.removeItem('reservationInfo');
			return response;
		}
		// implement SwalFlash here

		for (const key in data.errors) {
			if (data.errors.hasOwnProperty(key)) {
				alert(`${data.errors[key]}`);
			}
		}

		return response;

		// document.write(response.data);
	}).catch(response => {
		// console.log(response);

		// implement SwalFlash here

		// document.write(response.response.data);
		return response;
	});
	return response.data.success;
}