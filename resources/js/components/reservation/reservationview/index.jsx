import './style.css';

import { useState } from 'react';
import { redirect } from 'react-router-dom';
import axios from 'axios';

// Components
import ReservationConfirmation from '../reservationconfirmation/index';

import ReservationStatus from '../reservationsuccess/index';

// Under Development
export async function action() {

	const API = '/api/react/bookings/cancel-request';

	const token = document.querySelector('meta[name=csrf-token]').content;


	function getSessionItem() {
		return {
			control_number: sessionStorage.getItem('control_number'),
			cancellation_reason: sessionStorage.getItem('cancellation_reason'),
		};
	}

	function removeSessionItem() {
		sessionStorage.removeItem('control_number');
		sessionStorage.removeItem('cancellation_reason');
	}


	const { control_number, cancellation_reason } = getSessionItem();

	let isSuccess;

	await axios.post(`${API}`, {
		_token: token,
		control_no: control_number,
		reason: cancellation_reason,
	}).then(res => {
		console.log('after sending cancellation request:', res);
		removeSessionItem();
	}).catch(res => {
		console.log('error cancelling request:', res);
	});

	// if (isSuccess == true) {
	// 	const reservationsuccess = true;
	// 	sessionStorage.setItem('reservationsuccess', JSON.stringify(reservationsuccess));
	// 	return redirect('/reservation/success');
	// }


	// return redirect('/reservation');


	return redirect('/reservation/cancel');
}

export default function ReservationView() {
	const [reservationInfo, setReservationInfo] = useState();
	const [validationError, setValidationError] = useState();

	const [cancel_request, set_cancel_request] = useState(false);
	const [undo_cancel_request, set_undo_cancel_request] = useState(false);

	function getSessionItem() {
		return {
			control_number: sessionStorage.getItem('control_number'),
			cancellation_reason: sessionStorage.getItem('cancellation_reason'),
		};
	}

	function removeSessionItem() {
		sessionStorage.removeItem('control_number');
		sessionStorage.removeItem('cancellation_reason');
	}

	const handleCancelRequest = async () => {
		const API = '/api/react/bookings/cancel-request';

		const token = document.querySelector('meta[name=csrf-token]').content;

		const { control_number, cancellation_reason } = getSessionItem();

		const response = await axios.post(`${API}`, {
			_token: token,
			control_no: control_number,
			reason: cancellation_reason,
		}).then(res => {
			console.log('after sending cancellation request:', res);
			if (res.data.success) {
				removeSessionItem();
			} else {
				SwalFlash.error(
					"Input Error",
					res.data.errors[Object.keys(res.data.errors)[0]][0],
					true
				);
			}

			return res;
		}).catch(res => {
			console.log('error cancelling request:', res);
			return res;
		});

		console.log('response.data.success', response.data.success);
		if (!response.data.success) {
			console.warn('meee');
			return false;
		}
		set_cancel_request(response.data.success);
	}

	const handleUndoCancelRequest = async () => {
		const API = '/api/react/bookings/cancel-request/retract';

		const token = document.querySelector('meta[name=csrf-token]').content;

		const { control_number } = getSessionItem();

		const response = await axios.post(`${API}`, {
			_token: token,
			control_no: control_number,
		}).then(res => {
			console.log('after sending undo cancellation request:', res);
			if (res.data.success) {
				removeSessionItem();
			}

			return res;
		}).catch(res => {
			console.log('error cancelling request:', res);
			return res;
		});

		set_undo_cancel_request(response.data.success);
	}

	const handleViewClick = (control_number) => {
		getReservationInfo(control_number).then((response) => {
			const data = response.data;
			// console.log(data);

			if (data.type == 'validation') {
				SwalFlash.error(
					"Input Error",
					data.errors[Object.keys(data.errors)[0]][0],
					true
				);
				setValidationError(data.errors[Object.keys(data.errors)[0]][0]);
			}
			else if (data.type == 'finished') {
				SwalFlash.info(
					"Booking cannot be displayed",
					data.errors,
					true
				);
			}
			else {
				let booking = data.booking;
				let contact = booking.primary_contact_information;
				let menus = booking.menus;

				let name = contact.contact_name.split(" ");



				if (name.length == 1) {
					name[1] = name[0];
					name[0] = "";
				}

				let senior_high_count;
				let junior_count;
				let elementary_count;

				for (let i = 0; i < menus.length; i++) {
					if (menus[i].name == 'Adult') {
						senior_high_count = menus[i].pivot.count;
					}
					else if (menus[i].name == 'Junior') {
						junior_count = menus[i].pivot.count;
					}
					else if (menus[i].name == 'Elementary') {
						elementary_count = menus[i].pivot.count;
					}
				}


				console.log('booking:', booking);

				/**
				 * i prepared the required data for reservationInfo
				 */
				let reservationInfo = {
					first_name: name[1],
					last_name: name[0],
					email: contact.email,
					phone: booking.phone_numbers.split("|")[0],
					adult_senior: senior_high_count == null ? 0 : senior_high_count,
					junior: junior_count == null ? 0 : junior_count,
					elementary: elementary_count == null ? 0 : elementary_count,
					date: booking.reserved_at,
					starting_time: booking.start_at,
					extension: booking.extension,
					special_request: booking.special_request == null ? '' : booking.special_request,
					cancel_request_reason: booking.cancel_request_reason,
					isCancelled: booking.cancel_requested == 1 ? true : false,
				};


				setReservationInfo(reservationInfo);
			}
		});
	}

	async function getReservationInfo(control_number) {
		const API = '/api/react/bookings/view';
		const token = document.querySelector('meta[name=csrf-token]').content;

		const reservationInfo = await axios.post(`${API}`, {
			_token: token,
			control_no: control_number
		}).then(res => {
			console.log('after fetching reservation:', res);
			return res;
		}).catch(res => {
			// console.log(res);
		});
		return reservationInfo;
	}

	if (cancel_request) {
		return <ReservationStatus
			title={'Your cancel request has been sent!'}
			description={'We will inform you about your cancel request sooner'}
			link_label={'go back to reservation'}
			link={'/reservationselection'}
			bg_style={{ backgroundColor: '#B83939' }}
			icon_style={{ color: '#871A1A' }}
		/>;
	} else if (undo_cancel_request) {
		return <ReservationStatus
			title={'Your cancel request has been cancelled!'}
			description={'We will continue preparing your reservation'}
			link_label={'go back to reservation'}
			link={'/reservationselection'}
			bg_style={{ backgroundColor: '#1D7B3E' }}
			icon_style={{ color: '#00ff59a1' }}
		/>;
	}
	return (
		<div>
			<ViewReservation onViewClick={handleViewClick} validationError={validationError} />
			<hr />
			{
				reservationInfo == null ?
					<></>
					:
					<ReservationConfirmation
						forViewReservation={true}
						reservationInfo={reservationInfo}
						onCancelRequestClick={handleCancelRequest}
						onUndoCancelRequestClick={handleUndoCancelRequest}
					/>
			}
		</div>
	);
}

const ViewReservation = ({ onViewClick, validationError }) => {
	const [controlNumber, setControlNumber] = useState('');

	const handleInputControlNumber = (event) => {
		sessionStorage.setItem('control_number', event.target.value);
		setControlNumber(event.target.value);
	}
	const handleViewButton = () => {
		onViewClick(controlNumber);
	}

	return (
		<div className='container container-small'>
			<div className="m-5">
				<label htmlFor='control_no' className="form-label">Control Number</label>

				<div className="row g-2">
					<div className='col'>
						<input type='text' placeholder='Input your control number here...' onChange={handleInputControlNumber} className='form-control my-0' id='control_no' />
					</div>

					<div className='col-sm-auto text-end'>
						<button onClick={handleViewButton} className='btn btn-success'>View Reservation</button>
					</div>

					<div className="col-12">
						<small className="text-danger">{validationError}</small>
					</div>
				</div>
			</div>
		</div>
	);
}