import React from 'react';
import './Navigation.css';
import { FontAwesomeIcon } from '@fortawesome/react-fontawesome';
import { faBars } from '@fortawesome/free-solid-svg-icons';

// Navbars component (Bootstrap)
import Container from 'react-bootstrap/Container';
import Nav from 'react-bootstrap/Nav';
import Navbar from 'react-bootstrap/Navbar';


const Navigation = () =>
    <div className="Navigation">
      <Navbar bg="light" expand="lg" className="Navigation">
        <Container>
          <Navbar.Brand href="#home" className="Navigation-title">Party Color</Navbar.Brand>
          <Navbar.Toggle aria-controls="basic-navbar-nav" style={{ backgroundColor: '#322121' , border: '2px solid #B09898', borderRadius: '10px'}}>
            {/* to customize the icon style */}
            <FontAwesomeIcon icon={faBars} style={{ color: '#5B5555'}}/>
          </Navbar.Toggle>
          <Navbar.Collapse id="basic-navbar-nav"  style={{ textAlign: 'left'}}>
            <Nav className="me-auto">
              <Nav.Link href="#system" style={{ color: 'white' }}>System</Nav.Link>
              <Nav.Link href="#link" style={{ color: 'white' }}>Reservation</Nav.Link>
              <Nav.Link href="#announcement" style={{ color: 'white' }}>Announcement</Nav.Link>
              <Nav.Link href="#aboutus" style={{ color: 'white' }}>About Us</Nav.Link>
            </Nav>
          </Navbar.Collapse>
        </Container>
      </Navbar>
    </div>



export default Navigation;