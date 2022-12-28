import './App.css';

// for bootstrap
import 'bootstrap/dist/css/bootstrap.min.css';

import Navigation from './navbar/Navigation';

import Home from './Home';

// testing for calendar app
// import MyCalendar from './calendar/MyCalendar';

const App = () =>	
	<div className="App">
		<Navigation />
		<Home />
		{/* <MyCalendar /> */}
	</div>

export default App;
