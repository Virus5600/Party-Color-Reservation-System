import React from 'react';

import './style.css';

import ReactHtmlParser from 'react-html-parser';

import axios from 'axios';

import { useLoaderData, Link } from 'react-router-dom';

export async function loader({ params }) {
    let announcement;

    // if (sessionStorage.getItem('latestannouncement') == null) {

    //     const announcements = JSON.parse(sessionStorage.getItem('announcement'));
    //     const _announcements = announcements.filter(announcement => announcement.id == params.announcementId);
    //     return _announcements[0];
    // }

    // const announcements = JSON.parse(sessionStorage.getItem('latestannouncement'));
    // const _announcements = announcements.filter(announcement => announcement.id == params.announcementId);
    // console.log(_announcements[0]);
    // return _announcements[0];
    const API = 'api/react/announcements';
    await axios.get(`${API}/${params.announcementId}`).then(res => {
        announcement = res.data.announcement;
    }).catch(res => console.log(res));
    return announcement;
}

export default function AnnouncementContent() {
    const announcement = useLoaderData();

    return (
        <>
            <div className='AnnouncementContent'>
                <div>
                    <div className='AnnouncementContent-title'>
                        "{announcement.title}"
                    </div>

                    <div className='AnnouncementContent-summary'>
                        {announcement.summary}
                    </div>
                    <hr />
                    <br />

                    <div className='AnnouncementContent-content'>
                        {ReactHtmlParser(announcement.content)}
                    </div>
                </div>
            </div>

            <div className=' d-flex justify-content-center mt-2'>
                <Link to='/announcements' className="btn btn-primary">Back</Link>
            </div>
        </>



    );
}