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
            <div className='fs-3 w-75 mx-auto mt-5 bg-light p-3 rounded-1 text-center'>
                <div>
                    <h1 className='py-0'>
                        "{announcement.title}"
                    </h1>

                    <div className='fs-5 opacity-75'>
                        {announcement.summary}
                    </div>
                    <hr className='dark' />
                    <br />

                    <div className='fs-6'>

                        {ReactHtmlParser(announcement.content)}
                    </div>
                </div>
            </div>

            <div className=' d-flex justify-content-center mt-2'>
                <Link to='..' relative='path' className="btn btn-primary">Back</Link>
            </div>
        </>



    );
}