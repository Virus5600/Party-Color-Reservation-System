// for bootstrap
import 'bootstrap/dist/css/bootstrap.min.css';

// css styles
import './App.css';

// icons
import { FontAwesomeIcon } from '@fortawesome/react-fontawesome';
import { faClock, faPhone, faLocationArrow } from '@fortawesome/free-solid-svg-icons';

// images
import logo from './img/logo.png';
import mainImage from './img/mainImage.png';
import announcementImage from './img/announcement_image.png';
import locationImage from './img/location.png';
import appearanceImage from './img/appearance.png';

const App = () => {
  const listStyle = { 
	color: '#A52A2A', 
	fontWeight: 800, 
	fontSize: '25px', 
	listStyle: 'none', 
	display: 'inline', 
	marginRight: '20px' 
  };

  return (
	<div className='App'>
		<div className='Nav'>
			<div className='container-mb d-flex justify-content-between align-items-end'>
				<img src={logo} alt='logo' height='90' />
				<ul>
				<li style={listStyle}><a href='#Home'>Home</a></li>
				<li style={listStyle}><a href='#Reservation'>Reservation</a></li>
				<li style={listStyle}><a href='#Announcement'>Announcement</a></li>
				<li style={listStyle}><a href='#AboutUs'>About Us</a></li>
				</ul>
			</div>
		</div>

		<div className='main-image container-mb'>
		    <img src={mainImage} className='img-fluid'/>
		</div>

		<div className='quick-reservation'>


			<div className='Reservation'>
				<span className='title'>BBQ</span><br/>
				<span className='caption'>(including drink all you can)</span><br/>
				<span className='time'>2hrs</span><br/>
				<button className='reserve-button'>RESERVE</button>
			</div>


			<div className='Prices'>
				<div className='price-description'>
					<span className='person-type'>Adult・senior high</span><br/>
					<span className='price'>¥3,500</span>
				</div>
				<div className='price-description diff-style'>
					<span className='person-type'>junior high</span><br/>
					<span className='price'>¥2,000</span>
				</div>
				<div className='price-description'>
					<span className='person-type'>elementary</span><br/>
					<span className='price'>¥1,000</span>
				</div>
			</div>


		</div>

		<Announcement />
		<AboutUs />
		

	</div>
  );
};

const AboutUs = () => {
  return (
	<div className='AboutUs'>
			<h1>ABOUT US</h1>
			<div className='d-flex justify-content-evenly'>
				<TimeLocation />
				<Appearance />
			</div>
			
		</div>
  );
};

const Appearance = () => {
  return (
	<div className='Appearance'>
		<h2>APPEARANCE</h2>
		<img src={appearanceImage} alt='appearance' />
	</div>
  );	
};

const TimeLocation = () => {
  return (
	<div className='time-location'>
				<div className='AboutUs-description d-flex adjustment'>
					<div className='d-flex '>
						<FontAwesomeIcon icon={faClock} className='icon' />
						<p>17:00 - 22:00</p>
					</div>
					<div>
						<p className='closing'>CLOSED MONDAY/TUESDAY</p>
					</div>
				</div>
				<hr />
				<div className='AboutUs-description d-flex m-2'>
					<FontAwesomeIcon icon={faPhone} className='icon' />
					<p>080-3980-4560</p>
				</div>
				<hr />
				<div className='AboutUs-description '>
					<div className='d-flex m-2'>
						<FontAwesomeIcon icon={faLocationArrow} className='icon' />
						<p>3F, 1 Chome-2-12 Tsuboya, Naha, Okinawa 902-0065, Japan</p>
					</div>
					<div>
						<img className='img-fluid' src={locationImage} alt='location map of party color' />
					</div>
				</div>
			</div>
  );
};

const Announcement = () => {
  return (
	<div className='Announcement'>
			<h1>Announcement</h1>
			<div className='Announcement-list'>
				<AnnouncementItem /><hr />
				<AnnouncementItem /><hr />
				<AnnouncementItem /><hr />
				<AnnouncementItem />
			</div>
		</div>
  );
};

const AnnouncementItem = () => {
  return (
	<div className='Announcement-item'>
					<div className='Announcement-image'>
						<img className='img-fluid' src={announcementImage} />
					</div>
					<div className='Announcement-description'>
						<span>2021.1.5</span><br />
						<span>Halloween 15% Discount Promo</span><br />
						<span>BBQ & Drinks Plan Adult・senior high: ￥3,500 to ￥ 2,975...</span>
					</div>
				</div>
				
  );
};

export default App;
