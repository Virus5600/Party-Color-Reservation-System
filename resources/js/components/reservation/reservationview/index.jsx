import './style.css';

import { useState } from 'react';
import { redirect } from 'react-router-dom';
import axios from 'axios';

// Components
import ReservationConfirmation from '../reservationconfirmation/index';

// Under Development
export async function action() {
	const API = '/api/react/bookings/cancel-request';
	const token = document.querySelector('meta[name=csrf-token]').content;

	const result = await axios.post(`${API}`, {
		_token: token,
		control_no: "control number here",
		reason: "Cancellation reason. This is required."
	}).then(res => {
		console.log(res)
	}).catch(res => {
		console.log(res)
	});
	
	return redirect('/reservation/cancel');
}

export default function ReservationView() {
	const [reservationInfo, setReservationInfo] = useState();
	const [validationError, setValidationError] = useState();

	const handleViewClick = (control_number) => {
		getReservationInfo(control_number).then((response) => {
			let data = response.data;
			console.log(data);

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

				// For debugging and development
				console.log(booking);
				console.log(contact);
				console.log(menus);

				if (name.length == 1) {
					name[1] = name [0];
					name[0] = "";
				}
				
				let reservationInfo = {
					first_name: name[1],
					last_name: name[0],
					email: contact.email,
					phone: booking.phone_numbers.split("|")[0],
					adult_senior: '2',
					junior: '2',
					elementary: '3',
					date: booking.reserved_at,
					starting_time: booking.start_at,
					time_extension: booking.extension,
					special_request: booking.special_request,
				};
				
				sessionStorage.setItem('reservationInfo', JSON.stringify(reservationInfo));
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
			// console.log(res);
			return res;
		}).catch(res => {
			// console.log(res);
		});
		return reservationInfo;
	}

	return (
		<div>
			<ViewReservation onViewClick={handleViewClick} validationError={validationError}/>
			<hr />
			{
				reservationInfo == null ? <></> : <ReservationConfirmation forViewReservation={true} reservationInfo={reservationInfo} />
			}
		</div>
	);
}

const ViewReservation = ({ onViewClick, validationError }) => {
	const [controlNumber, setControlNumber] = useState('');

	const handleInputControlNumber = (event) => {
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
						<input type='text' placeholder='Input your control number here...' onChange={handleInputControlNumber} className='form-control my-0' id='control_no'/>
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