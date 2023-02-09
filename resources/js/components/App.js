// for bootstrap
import 'bootstrap/dist/css/bootstrap.min.css';
import 'bootstrap/dist/js/bootstrap.min.js';

import Navigation from './navigation';
import Home from './home';

import './App.css';


const App = () => {

	return (
		<div className='App'>
			<Navigation>
				<Home />
			</Navigation>
		</div>
	);

};


export default App;
