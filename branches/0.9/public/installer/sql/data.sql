--
-- PostgreSQL database dump
--

SET statement_timeout = 0;
SET client_encoding = 'UTF8';
SET standard_conforming_strings = off;
SET check_function_bodies = false;
SET client_min_messages = warning;
SET escape_string_warning = off;

--
-- Name: feedback; Type: SCHEMA; Schema: -; Owner: -
--

CREATE SCHEMA feedback;


--
-- Name: plpgsql; Type: PROCEDURAL LANGUAGE; Schema: -; Owner: -
--

CREATE PROCEDURAL LANGUAGE plpgsql;


SET search_path = public, pg_catalog;

--
-- Name: fix_order_on_del(); Type: FUNCTION; Schema: public; Owner: -
--

CREATE FUNCTION fix_order_on_del() RETURNS trigger
    LANGUAGE plpgsql
    AS '
DECLARE
displayorder_x sessions_presentations.displayorder%TYPE;
parent_id_x sessions_presentations.session_id%TYPE;
maxdisplayorder integer;
BEGIN
displayorder_x := OLD.displayorder;
parent_id_x := OLD.session_id;
SELECT INTO maxdisplayorder MAX(displayorder) FROM sessions_presentations WHERE session_id = parent_id_x;
IF displayorder_x < maxdisplayorder THEN
    -- this record does not have the highest displayorder in its tree
EXECUTE ''UPDATE sessions_presentations SET displayorder = (displayorder-1)
WHERE session_id = '' || parent_id_x || '' AND displayorder > '' || displayorder_x || '''';
RAISE NOTICE ''Changing displayorder to remove gaps'';
END IF;
RETURN NULL;
END;
';


--
-- Name: fix_order_on_insert(); Type: FUNCTION; Schema: public; Owner: -
--

CREATE FUNCTION fix_order_on_insert() RETURNS trigger
    LANGUAGE plpgsql
    AS '
DECLARE
displayorder_x sessions_presentations.displayorder%TYPE;
parent_id_x sessions_presentations.session_id%TYPE;
BEGIN
parent_id_x = NEW.session_id;
SELECT INTO displayorder_x MAX(displayorder) + 1 FROM sessions_presentations WHERE session_id = parent_id_x;
IF displayorder_x IS NULL THEN
    displayorder_x := 1;
END IF;
NEW.displayorder = displayorder_x;
RETURN NEW;
END;
';


--
-- Name: last_modified(); Type: FUNCTION; Schema: public; Owner: -
--

CREATE FUNCTION last_modified() RETURNS trigger
    LANGUAGE plpgsql
    AS '
BEGIN
   NEW.updated = now(); 
   RETURN NEW;
END;
';


SET search_path = feedback, pg_catalog;

SET default_tablespace = '';

SET default_with_oids = false;

--
-- Name: codes; Type: TABLE; Schema: feedback; Owner: -; Tablespace: 
--

CREATE TABLE codes (
    code_id integer NOT NULL,
    uuid character(36)
);


--
-- Name: COLUMN codes.uuid; Type: COMMENT; Schema: feedback; Owner: -
--

COMMENT ON COLUMN codes.uuid IS 'unique code given out to conference visitor';


--
-- Name: codes_code_id_seq; Type: SEQUENCE; Schema: feedback; Owner: -
--

CREATE SEQUENCE codes_code_id_seq
    START WITH 1
    INCREMENT BY 1
    MAXVALUE 989898989989
    NO MINVALUE
    CACHE 1;


--
-- Name: codes_code_id_seq1; Type: SEQUENCE; Schema: feedback; Owner: -
--

CREATE SEQUENCE codes_code_id_seq1
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


--
-- Name: codes_code_id_seq1; Type: SEQUENCE OWNED BY; Schema: feedback; Owner: -
--

ALTER SEQUENCE codes_code_id_seq1 OWNED BY codes.code_id;


SET default_with_oids = true;

--
-- Name: general; Type: TABLE; Schema: feedback; Owner: -; Tablespace: 
--

CREATE TABLE general (
    id integer NOT NULL,
    rating smallint,
    why_other_spec text,
    heard_other_spec text,
    been_before text,
    come_again text,
    conf_hear text,
    part_reasons text
);


--
-- Name: logistics; Type: TABLE; Schema: feedback; Owner: -; Tablespace: 
--

CREATE TABLE logistics (
    id integer NOT NULL,
    doc_before smallint,
    doc_during smallint,
    registration smallint,
    hotel_booking smallint,
    onsite_reg smallint,
    transport smallint,
    venue smallint,
    network smallint,
    catering smallint,
    social smallint,
    sponsors smallint,
    remarks_doc_before text,
    remarks_doc_during text,
    remarks_registration text,
    remarks_hotel_booking text,
    remarks_onsite_reg text,
    remarks_transport text,
    remarks_venue text,
    remarks_network text,
    remarks_catering text,
    remarks_social text,
    remarks_sponsors text,
    updated timestamp(0) without time zone DEFAULT now(),
    comments text,
    website smallint,
    core smallint,
    social_media smallint,
    social_events smallint,
    remarks_website text,
    remarks_core text,
    remarks_social_media text,
    remarks_social_events text,
    remarks_vfm_accomodation text,
    remarks_vfm_regfee text,
    vfm_accomodation text,
    vfm_regfee text
);


--
-- Name: participant; Type: TABLE; Schema: feedback; Owner: -; Tablespace: 
--

CREATE TABLE participant (
    id integer NOT NULL,
    occupation text,
    interest text,
    country text,
    org_type text,
    occupation_other text,
    interest_other text,
    org_type_other text
);


--
-- Name: presentations; Type: TABLE; Schema: feedback; Owner: -; Tablespace: 
--

CREATE TABLE presentations (
    id integer NOT NULL,
    presentation_id integer NOT NULL,
    comments text,
    rating smallint
);


--
-- Name: COLUMN presentations.id; Type: COMMENT; Schema: feedback; Owner: -
--

COMMENT ON COLUMN presentations.id IS 'unique code given out to conference visitor';


--
-- Name: programme; Type: TABLE; Schema: feedback; Owner: -; Tablespace: 
--

CREATE TABLE programme (
    id integer NOT NULL,
    best_stuff text,
    worst_stuff text,
    comments text,
    exhibition smallint,
    meetings smallint,
    lightning smallint,
    poster smallint,
    remarks_exhibition text,
    remarks_poster text,
    remarks_lightning text,
    remarks_meetings text
);


SET search_path = public, pg_catalog;

--
-- Name: conferences_conference_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE conferences_conference_id_seq
    START WITH 1
    INCREMENT BY 1
    MAXVALUE 99999999
    NO MINVALUE
    CACHE 1;


--
-- Name: conferences; Type: TABLE; Schema: public; Owner: -; Tablespace: 
--

CREATE TABLE conferences (
    conference_id integer DEFAULT nextval('conferences_conference_id_seq'::regclass) NOT NULL,
    abbreviation text NOT NULL,
    name text,
    description text,
    submit_start timestamp(0) with time zone,
    submit_end timestamp(0) with time zone,
    email text,
    review_start timestamp with time zone,
    review_end timestamp with time zone,
    hostname text NOT NULL,
    review_visible timestamp with time zone,
    layout boolean,
    googlemapskey text,
    gcal_url text,
    gcal_username text,
    gcal_password text,
    feedback_end timestamp(0) with time zone,
    navigation boolean,
    stream_url text,
    timezone text
);
ALTER TABLE ONLY conferences ALTER COLUMN hostname SET STATISTICS 100;


--
-- Name: COLUMN conferences.abbreviation; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN conferences.abbreviation IS 'This is part of the vhost name. Only lowercase alphanumeric characters are allowed.';


--
-- Name: COLUMN conferences.hostname; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN conferences.hostname IS 'HTTP host name of the conference web site';


--
-- Name: COLUMN conferences.layout; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN conferences.layout IS 'use custom layout';


--
-- Name: COLUMN conferences.navigation; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN conferences.navigation IS 'use custom navigation';


--
-- Name: deadlines; Type: TABLE; Schema: public; Owner: -; Tablespace: 
--

CREATE TABLE deadlines (
    deadline_id integer NOT NULL,
    conference_id integer,
    controller text NOT NULL,
    privilege text,
    tstart timestamp(0) with time zone NOT NULL,
    tend timestamp(0) with time zone NOT NULL
);


--
-- Name: deadlines_deadline_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE deadlines_deadline_id_seq
    START WITH 1
    INCREMENT BY 1
    MAXVALUE 98798987978
    NO MINVALUE
    CACHE 1;


--
-- Name: deadlines_deadline_id_seq1; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE deadlines_deadline_id_seq1
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


--
-- Name: deadlines_deadline_id_seq1; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE deadlines_deadline_id_seq1 OWNED BY deadlines.deadline_id;


--
-- Name: eventcategories; Type: TABLE; Schema: public; Owner: -; Tablespace: 
--

CREATE TABLE eventcategories (
    eventcategory_id integer NOT NULL,
    category text
);


--
-- Name: eventcategories_eventcategory_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE eventcategories_eventcategory_id_seq
    START WITH 1
    INCREMENT BY 1
    MAXVALUE 9898343432
    NO MINVALUE
    CACHE 1;


--
-- Name: eventcategories_eventcategory_id_seq1; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE eventcategories_eventcategory_id_seq1
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


--
-- Name: eventcategories_eventcategory_id_seq1; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE eventcategories_eventcategory_id_seq1 OWNED BY eventcategories.eventcategory_id;


--
-- Name: eventlog; Type: TABLE; Schema: public; Owner: -; Tablespace: 
--

CREATE TABLE eventlog (
    event_type text NOT NULL,
    "timestamp" timestamp(0) with time zone NOT NULL,
    conference_id integer NOT NULL
);


--
-- Name: eventlog_eventlog_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE eventlog_eventlog_id_seq
    START WITH 1
    INCREMENT BY 1
    MAXVALUE 9899898989999
    NO MINVALUE
    CACHE 1;


--
-- Name: events; Type: TABLE; Schema: public; Owner: -; Tablespace: 
--

CREATE TABLE events (
    event_id integer NOT NULL,
    title text,
    tstart timestamp(0) with time zone,
    tend timestamp(0) with time zone,
    location_id integer,
    description text,
    closed boolean,
    cancelled boolean,
    category_id integer,
    registration text,
    inserted timestamp(0) with time zone DEFAULT now(),
    updated timestamp(0) with time zone,
    file_id integer,
    conference_id integer,
    persons text
);


--
-- Name: events_event_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE events_event_id_seq
    START WITH 1
    INCREMENT BY 1
    MAXVALUE 999999999
    NO MINVALUE
    CACHE 1;


--
-- Name: events_event_id_seq1; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE events_event_id_seq1
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


--
-- Name: events_event_id_seq1; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE events_event_id_seq1 OWNED BY events.event_id;


--
-- Name: files_file_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE files_file_id_seq
    START WITH 1
    INCREMENT BY 1
    MAXVALUE 99845645645645
    NO MINVALUE
    CACHE 1;


--
-- Name: files; Type: TABLE; Schema: public; Owner: -; Tablespace: 
--

CREATE TABLE files (
    file_id integer DEFAULT nextval('files_file_id_seq'::regclass) NOT NULL,
    filename text,
    filesize integer,
    mimetype text,
    modified timestamp(0) with time zone DEFAULT now(),
    filehash text,
    location text,
    filename_orig text,
    filetype integer
);


--
-- Name: filetypes_filetype_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE filetypes_filetype_id_seq
    START WITH 1
    INCREMENT BY 1
    MAXVALUE 9823423423
    NO MINVALUE
    CACHE 1;


--
-- Name: filetypes; Type: TABLE; Schema: public; Owner: -; Tablespace: 
--

CREATE TABLE filetypes (
    filetype_id integer DEFAULT nextval('filetypes_filetype_id_seq'::regclass) NOT NULL,
    name text NOT NULL
);


--
-- Name: locations_location_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE locations_location_id_seq
    START WITH 1
    INCREMENT BY 1
    MAXVALUE 9999999999
    NO MINVALUE
    CACHE 1;


--
-- Name: locations; Type: TABLE; Schema: public; Owner: -; Tablespace: 
--

CREATE TABLE locations (
    location_id integer DEFAULT nextval('locations_location_id_seq'::regclass) NOT NULL,
    name text NOT NULL,
    abbreviation text NOT NULL,
    capacity smallint,
    comments text,
    conference_id integer NOT NULL,
    type integer,
    file_id integer,
    address text,
    lat numeric(10,6),
    lng numeric(10,6)
);


SET default_with_oids = false;

--
-- Name: posters; Type: TABLE; Schema: public; Owner: -; Tablespace: 
--

CREATE TABLE posters (
    poster_id integer NOT NULL,
    title text,
    description text,
    inserted timestamp(0) with time zone DEFAULT now(),
    persons text,
    file_id integer,
    updated timestamp(0) with time zone,
    conference_id integer
);
ALTER TABLE ONLY posters ALTER COLUMN poster_id SET STATISTICS 0;
ALTER TABLE ONLY posters ALTER COLUMN persons SET STATISTICS 0;


--
-- Name: posters_poster_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE posters_poster_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


--
-- Name: posters_poster_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE posters_poster_id_seq OWNED BY posters.poster_id;


--
-- Name: presentation_file_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE presentation_file_id_seq
    START WITH 1
    INCREMENT BY 1
    MAXVALUE 999999999999
    NO MINVALUE
    CACHE 1;


--
-- Name: presentation_tag_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE presentation_tag_id_seq
    START WITH 1
    INCREMENT BY 1
    MAXVALUE 9999999
    NO MINVALUE
    CACHE 1;


--
-- Name: presentations_presentation_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE presentations_presentation_id_seq
    START WITH 1
    INCREMENT BY 1
    MAXVALUE 9999999999999
    NO MINVALUE
    CACHE 1;


SET default_with_oids = true;

--
-- Name: presentations; Type: TABLE; Schema: public; Owner: -; Tablespace: 
--

CREATE TABLE presentations (
    presentation_id integer DEFAULT nextval('presentations_presentation_id_seq'::regclass) NOT NULL,
    submission_id integer,
    title text NOT NULL,
    abstract text,
    inserted timestamp(0) with time zone DEFAULT now(),
    conference_id integer,
    authors text,
    updated timestamp(0) with time zone,
    image text
);


--
-- Name: presentations_files; Type: TABLE; Schema: public; Owner: -; Tablespace: 
--

CREATE TABLE presentations_files (
    presentation_file_id integer DEFAULT nextval('presentation_file_id_seq'::regclass) NOT NULL,
    presentation_id integer,
    file_id integer
);
ALTER TABLE ONLY presentations_files ALTER COLUMN file_id SET STATISTICS 0;


--
-- Name: presentations_tags; Type: TABLE; Schema: public; Owner: -; Tablespace: 
--

CREATE TABLE presentations_tags (
    presentation_tag_id integer DEFAULT nextval('presentation_tag_id_seq'::regclass) NOT NULL,
    presentation_id integer,
    tag_id integer
);


--
-- Name: presentations_users_presentation_user_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE presentations_users_presentation_user_id_seq
    START WITH 1
    INCREMENT BY 1
    MAXVALUE 989899999
    NO MINVALUE
    CACHE 1;


--
-- Name: presentations_users; Type: TABLE; Schema: public; Owner: -; Tablespace: 
--

CREATE TABLE presentations_users (
    presentation_user_id integer DEFAULT nextval('presentations_users_presentation_user_id_seq'::regclass) NOT NULL,
    presentation_id integer,
    user_id integer
);


--
-- Name: reviewers_submissions_reviewer_submission_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE reviewers_submissions_reviewer_submission_id_seq
    START WITH 1
    INCREMENT BY 1
    MAXVALUE 99999999999999
    NO MINVALUE
    CACHE 1;


--
-- Name: reviewers_submissions; Type: TABLE; Schema: public; Owner: -; Tablespace: 
--

CREATE TABLE reviewers_submissions (
    reviewer_submission_id integer DEFAULT nextval('reviewers_submissions_reviewer_submission_id_seq'::regclass) NOT NULL,
    user_id integer,
    submission_id integer
);
ALTER TABLE ONLY reviewers_submissions ALTER COLUMN user_id SET STATISTICS 0;


--
-- Name: reviews_review_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE reviews_review_id_seq
    START WITH 1
    INCREMENT BY 1
    MAXVALUE 999999999
    NO MINVALUE
    CACHE 1;


--
-- Name: reviews; Type: TABLE; Schema: public; Owner: -; Tablespace: 
--

CREATE TABLE reviews (
    review_id integer DEFAULT nextval('reviews_review_id_seq'::regclass) NOT NULL,
    submission_id integer,
    inserted timestamp(0) with time zone DEFAULT now(),
    suitability_conf smallint,
    importance smallint,
    rating smallint,
    self_assessment smallint,
    quality smallint,
    comments_presentation text,
    comments_pc text,
    comments_authors text,
    user_id integer
);


--
-- Name: COLUMN reviews.submission_id; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN reviews.submission_id IS 'what submission is being reviewed?';


--
-- Name: COLUMN reviews.user_id; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN reviews.user_id IS 'who did the review?';


--
-- Name: roles; Type: TABLE; Schema: public; Owner: -; Tablespace: 
--

CREATE TABLE roles (
    role_id integer NOT NULL,
    name text
);


--
-- Name: roles_role_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE roles_role_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


--
-- Name: roles_role_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE roles_role_id_seq OWNED BY roles.role_id;


--
-- Name: session_evaluation; Type: TABLE; Schema: public; Owner: -; Tablespace: 
--

CREATE TABLE session_evaluation (
    session_evaluation_id integer NOT NULL,
    attendees integer,
    comments text,
    session_id integer,
    inserted timestamp(0) with time zone DEFAULT now(),
    user_id integer,
    updated timestamp(0) with time zone
);


--
-- Name: session_evaluation_session_evaluation_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE session_evaluation_session_evaluation_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


--
-- Name: session_evaluation_session_evaluation_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE session_evaluation_session_evaluation_id_seq OWNED BY session_evaluation.session_evaluation_id;


--
-- Name: sessions_session_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE sessions_session_id_seq
    START WITH 1
    INCREMENT BY 1
    MAXVALUE 999657545645
    NO MINVALUE
    CACHE 1;


--
-- Name: sessions; Type: TABLE; Schema: public; Owner: -; Tablespace: 
--

CREATE TABLE sessions (
    session_id integer DEFAULT nextval('sessions_session_id_seq'::regclass) NOT NULL,
    title text,
    description text,
    updated timestamp(0) without time zone,
    logo text,
    tag_id integer,
    location_id integer,
    timeslot_id integer,
    conference_id integer,
    gcal_event_id text,
    inserted timestamp(0) with time zone DEFAULT now()
);


--
-- Name: sessions_files; Type: TABLE; Schema: public; Owner: -; Tablespace: 
--

CREATE TABLE sessions_files (
    session_file_id integer NOT NULL,
    session_id integer,
    file_id integer
);


--
-- Name: sessions_files_session_file_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE sessions_files_session_file_id_seq
    START WITH 1
    INCREMENT BY 1
    MAXVALUE 99999999999
    NO MINVALUE
    CACHE 1;


--
-- Name: sessions_presentations_session_presentation_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE sessions_presentations_session_presentation_id_seq
    START WITH 1
    INCREMENT BY 1
    MAXVALUE 9898989899999
    NO MINVALUE
    CACHE 1;


--
-- Name: sessions_presentations; Type: TABLE; Schema: public; Owner: -; Tablespace: 
--

CREATE TABLE sessions_presentations (
    session_presentation_id integer DEFAULT nextval('sessions_presentations_session_presentation_id_seq'::regclass) NOT NULL,
    session_id integer,
    presentation_id integer,
    displayorder smallint
);


--
-- Name: sessions_tags; Type: TABLE; Schema: public; Owner: -; Tablespace: 
--

CREATE TABLE sessions_tags (
    session_tag_id integer NOT NULL,
    session_id integer,
    tag_id integer
);


--
-- Name: sessions_users_session_user_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE sessions_users_session_user_id_seq
    START WITH 1
    INCREMENT BY 1
    MAXVALUE 98956565656
    NO MINVALUE
    CACHE 1;


--
-- Name: sessions_users; Type: TABLE; Schema: public; Owner: -; Tablespace: 
--

CREATE TABLE sessions_users (
    session_user_id integer DEFAULT nextval('sessions_users_session_user_id_seq'::regclass) NOT NULL,
    session_id integer,
    user_id integer
);


--
-- Name: submission_status_submission_status_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE submission_status_submission_status_id_seq
    START WITH 1
    INCREMENT BY 1
    MAXVALUE 9898989999999
    NO MINVALUE
    CACHE 1;


--
-- Name: submission_status; Type: TABLE; Schema: public; Owner: -; Tablespace: 
--

CREATE TABLE submission_status (
    submission_status_id integer DEFAULT nextval('submission_status_submission_status_id_seq'::regclass) NOT NULL,
    submission_id integer NOT NULL,
    status integer,
    session_id integer
);


--
-- Name: submissions_submission_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE submissions_submission_id_seq
    START WITH 1
    INCREMENT BY 1
    MAXVALUE 99999999999
    NO MINVALUE
    CACHE 1;


--
-- Name: submissions; Type: TABLE; Schema: public; Owner: -; Tablespace: 
--

CREATE TABLE submissions (
    submission_id integer DEFAULT nextval('submissions_submission_id_seq'::regclass) NOT NULL,
    date timestamp(0) with time zone DEFAULT now(),
    title text,
    target_audience text,
    publish_paper text,
    comment text,
    file_id integer NOT NULL,
    conference_id integer
);


--
-- Name: subscribers_sessions; Type: TABLE; Schema: public; Owner: -; Tablespace: 
--

CREATE TABLE subscribers_sessions (
    subscriber_session_id integer NOT NULL,
    user_id integer NOT NULL,
    session_id integer NOT NULL
);


--
-- Name: subscribers_sessions_subscriber_session_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE subscribers_sessions_subscriber_session_id_seq
    START WITH 1
    INCREMENT BY 1
    MAXVALUE 999999999999999
    NO MINVALUE
    CACHE 1;


--
-- Name: subscribers_sessions_subscriber_session_id_seq1; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE subscribers_sessions_subscriber_session_id_seq1
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


--
-- Name: subscribers_sessions_subscriber_session_id_seq1; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE subscribers_sessions_subscriber_session_id_seq1 OWNED BY subscribers_sessions.subscriber_session_id;


--
-- Name: tags_tag_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE tags_tag_id_seq
    START WITH 1
    INCREMENT BY 1
    MAXVALUE 99999999999
    NO MINVALUE
    CACHE 1;


--
-- Name: tags; Type: TABLE; Schema: public; Owner: -; Tablespace: 
--

CREATE TABLE tags (
    tag_id integer DEFAULT nextval('tags_tag_id_seq'::regclass) NOT NULL
);


--
-- Name: timeslot_types; Type: TABLE; Schema: public; Owner: -; Tablespace: 
--

CREATE TABLE timeslot_types (
    timeslot_type_id integer NOT NULL,
    name text NOT NULL
);


--
-- Name: timeslots_timeslot_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE timeslots_timeslot_id_seq
    START WITH 1
    INCREMENT BY 1
    MAXVALUE 999999999
    NO MINVALUE
    CACHE 1;


--
-- Name: timeslots; Type: TABLE; Schema: public; Owner: -; Tablespace: 
--

CREATE TABLE timeslots (
    timeslot_id integer DEFAULT nextval('timeslots_timeslot_id_seq'::regclass) NOT NULL,
    tstart timestamp(0) with time zone NOT NULL,
    tend timestamp(0) with time zone NOT NULL,
    number smallint,
    type smallint,
    conference_id integer NOT NULL
);


--
-- Name: user_role; Type: TABLE; Schema: public; Owner: -; Tablespace: 
--

CREATE TABLE user_role (
    user_role_id integer NOT NULL,
    user_id integer,
    role_id integer,
    conf_id integer
);


--
-- Name: COLUMN user_role.user_id; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN user_role.user_id IS 'cascading - so if I remove user, it will also remove their entry here';


--
-- Name: COLUMN user_role.conf_id; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN user_role.conf_id IS 'Cascading: if conference is removed, all entries with that conference here will also be deleted.';


--
-- Name: user_role_user_role_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE user_role_user_role_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


--
-- Name: user_role_user_role_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE user_role_user_role_id_seq OWNED BY user_role.user_role_id;


--
-- Name: useraudit; Type: TABLE; Schema: public; Owner: -; Tablespace: 
--

CREATE TABLE useraudit (
    useraudit_id integer NOT NULL,
    email text,
    fname text,
    lname text,
    email_text text,
    organisation text,
    user_id integer
);


--
-- Name: useraudit_useraudit_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE useraudit_useraudit_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


--
-- Name: useraudit_useraudit_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE useraudit_useraudit_id_seq OWNED BY useraudit.useraudit_id;


--
-- Name: users_user_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE users_user_id_seq
    START WITH 1
    INCREMENT BY 1
    MAXVALUE 99999999999999999
    NO MINVALUE
    CACHE 1;


--
-- Name: users; Type: TABLE; Schema: public; Owner: -; Tablespace: 
--

CREATE TABLE users (
    user_id integer DEFAULT nextval('users_user_id_seq'::regclass) NOT NULL,
    fname text,
    lname text,
    organisation text,
    active boolean,
    inserted timestamp(0) with time zone DEFAULT now(),
    email text,
    lastlogin timestamp(0) with time zone,
    smart_id text,
    country text,
    profile text,
    jobtitle text,
    file_id integer,
    invite text
);


--
-- Name: users_submissions_user_submission_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE users_submissions_user_submission_id_seq
    START WITH 1
    INCREMENT BY 1
    MAXVALUE 9999999999999999
    NO MINVALUE
    CACHE 1;


--
-- Name: users_submissions; Type: TABLE; Schema: public; Owner: -; Tablespace: 
--

CREATE TABLE users_submissions (
    user_submission_id integer DEFAULT nextval('users_submissions_user_submission_id_seq'::regclass) NOT NULL,
    user_id integer,
    submission_id integer
);


--
-- Name: vw_events; Type: VIEW; Schema: public; Owner: -
--

CREATE VIEW vw_events AS
    SELECT e.conference_id, e.event_id, e.title, e.tstart, e.tend, e.location_id, e.description, e.closed, e.cancelled, e.category_id, e.registration, e.inserted, e.updated, e.file_id, e.persons, c.category, l.name AS location, l.address AS location_address FROM events e, eventcategories c, locations l WHERE ((e.category_id = c.eventcategory_id) AND (e.location_id = l.location_id));


--
-- Name: vw_files; Type: VIEW; Schema: public; Owner: -
--

CREATE VIEW vw_files AS
    SELECT f.file_id, f.filename, f.filesize, f.mimetype, f.modified, f.filehash, f.filename_orig, ft.name AS core_filetype FROM (files f LEFT JOIN filetypes ft ON ((f.filetype = ft.filetype_id)));


--
-- Name: vw_presentation_files; Type: VIEW; Schema: public; Owner: -
--

CREATE VIEW vw_presentation_files AS
    SELECT pf.presentation_id, f.file_id, f.filename, f.filesize, f.mimetype, f.modified, f.filehash, f.filename_orig, f.core_filetype FROM (presentations_files pf LEFT JOIN vw_files f ON ((pf.file_id = f.file_id)));


--
-- Name: vw_presentations; Type: VIEW; Schema: public; Owner: -
--

CREATE VIEW vw_presentations AS
    SELECT p.presentation_id, p.title AS presentation_title, p.conference_id, p.inserted, u.email, s.session_id, s.title AS session_title FROM ((((presentations p LEFT JOIN presentations_users pu ON ((p.presentation_id = pu.presentation_id))) LEFT JOIN users u ON ((pu.user_id = u.user_id))) LEFT JOIN sessions_presentations sp ON ((sp.presentation_id = p.presentation_id))) LEFT JOIN sessions s ON ((s.session_id = sp.session_id)));


--
-- Name: vw_presentations_speakers; Type: VIEW; Schema: public; Owner: -
--

CREATE VIEW vw_presentations_speakers AS
    SELECT pu.user_id, p.presentation_id, p.presentation_title, p.conference_id, p.inserted, p.email, p.session_id, p.session_title FROM (presentations_users pu LEFT JOIN vw_presentations p ON ((pu.presentation_id = p.presentation_id)));


--
-- Name: vw_reviews; Type: VIEW; Schema: public; Owner: -
--

CREATE VIEW vw_reviews AS
    SELECT r.review_id, r.submission_id, r.inserted, r.suitability_conf, r.importance, r.rating, r.self_assessment, r.quality, r.comments_presentation, r.comments_pc, r.comments_authors, r.user_id, u.email, u.fname, u.lname, u.organisation FROM (reviews r LEFT JOIN users u ON ((r.user_id = u.user_id)));


--
-- Name: vw_reviewstats; Type: VIEW; Schema: public; Owner: -
--

CREATE VIEW vw_reviewstats AS
    SELECT reviews.submission_id, min(reviews.inserted) AS review_first, max(reviews.inserted) AS review_last, count(*) AS review_count FROM reviews GROUP BY reviews.submission_id;


--
-- Name: vw_session_files; Type: VIEW; Schema: public; Owner: -
--

CREATE VIEW vw_session_files AS
    SELECT sf.session_id, f.file_id, f.filename, f.filesize, f.mimetype, f.modified, f.filehash, f.filename_orig, f.core_filetype FROM (sessions_files sf LEFT JOIN vw_files f ON ((sf.file_id = f.file_id)));


--
-- Name: vw_session_presentations; Type: VIEW; Schema: public; Owner: -
--

CREATE VIEW vw_session_presentations AS
    SELECT sp.session_id, p.presentation_id, p.title, s.title AS session_title, p.inserted AS updated FROM ((sessions_presentations sp LEFT JOIN presentations p ON ((sp.presentation_id = p.presentation_id))) LEFT JOIN sessions s ON ((s.session_id = sp.session_id)));


--
-- Name: vw_sessions; Type: VIEW; Schema: public; Owner: -
--

CREATE VIEW vw_sessions AS
    SELECT s.session_id, s.gcal_event_id, s.title, s.description, s.updated, s.logo, s.tag_id, s.location_id, s.timeslot_id, s.conference_id, l.name AS location_name, l.abbreviation AS location_abbreviation, l.address AS location_address, t.tstart, t.tend, t.number FROM ((sessions s LEFT JOIN locations l ON ((l.location_id = s.location_id))) LEFT JOIN timeslots t ON ((t.timeslot_id = s.timeslot_id)));


--
-- Name: vw_sessions_chairs; Type: VIEW; Schema: public; Owner: -
--

CREATE VIEW vw_sessions_chairs AS
    SELECT su.user_id, s.session_id, s.title, s.description, s.updated, s.logo, s.tag_id, s.location_id, s.timeslot_id, s.conference_id, s.location_name, s.location_abbreviation, s.tstart, s.tend FROM (sessions_users su LEFT JOIN vw_sessions s ON ((su.session_id = s.session_id)));


--
-- Name: vw_sessions_speakers; Type: VIEW; Schema: public; Owner: -
--

CREATE VIEW vw_sessions_speakers AS
    SELECT sessions_presentations.session_id, sessions_presentations.presentation_id, users.user_id, users.fname, users.lname, users.email, users.organisation FROM users, presentations_users, sessions_presentations WHERE ((sessions_presentations.presentation_id = presentations_users.presentation_id) AND (users.user_id = presentations_users.user_id)) ORDER BY sessions_presentations.displayorder;


--
-- Name: vw_sessionstats; Type: VIEW; Schema: public; Owner: -
--

CREATE VIEW vw_sessionstats AS
    SELECT sessions.title, sessions.session_id, st.submission_id, st.status FROM (submission_status st LEFT JOIN sessions ON ((st.session_id = sessions.session_id)));


--
-- Name: vw_speakers; Type: VIEW; Schema: public; Owner: -
--

CREATE VIEW vw_speakers AS
    SELECT pu.presentation_id, u.user_id, u.fname, u.lname, u.email, u.organisation FROM (presentations_users pu LEFT JOIN users u ON ((u.user_id = pu.user_id)));


--
-- Name: vw_submissions; Type: VIEW; Schema: public; Owner: -
--

CREATE VIEW vw_submissions AS
    SELECT s.submission_id, s.date, s.title, s.target_audience, s.publish_paper, s.comment, s.file_id, u.user_id, u.fname, u.lname, u.organisation, u.active, u.inserted, u.email, u.lastlogin, rs.review_first, rs.review_last, rs.review_count, s.conference_id, st.title AS session_title, st.session_id, st.status, s.date AS submission_insert FROM ((((submissions s LEFT JOIN users_submissions us ON ((us.submission_id = s.submission_id))) LEFT JOIN users u ON ((u.user_id = us.user_id))) LEFT JOIN vw_reviewstats rs ON ((rs.submission_id = s.submission_id))) LEFT JOIN vw_sessionstats st ON ((st.submission_id = s.submission_id)));


--
-- Name: vw_users; Type: VIEW; Schema: public; Owner: -
--

CREATE VIEW vw_users AS
    SELECT u.user_id, u.fname, u.lname, u.organisation, u.active, u.inserted, u.email, u.lastlogin, u.smart_id, u.country, u.profile, u.jobtitle, u.file_id, u.invite, r.role_id, r.name AS role_name FROM ((users u LEFT JOIN user_role ur ON ((u.user_id = ur.user_id))) LEFT JOIN roles r ON ((r.role_id = ur.role_id)));


SET search_path = feedback, pg_catalog;

--
-- Name: code_id; Type: DEFAULT; Schema: feedback; Owner: -
--

ALTER TABLE codes ALTER COLUMN code_id SET DEFAULT nextval('codes_code_id_seq1'::regclass);


SET search_path = public, pg_catalog;

--
-- Name: deadline_id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE deadlines ALTER COLUMN deadline_id SET DEFAULT nextval('deadlines_deadline_id_seq1'::regclass);


--
-- Name: eventcategory_id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE eventcategories ALTER COLUMN eventcategory_id SET DEFAULT nextval('eventcategories_eventcategory_id_seq1'::regclass);


--
-- Name: event_id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE events ALTER COLUMN event_id SET DEFAULT nextval('events_event_id_seq1'::regclass);


--
-- Name: poster_id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE posters ALTER COLUMN poster_id SET DEFAULT nextval('posters_poster_id_seq'::regclass);


--
-- Name: role_id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE roles ALTER COLUMN role_id SET DEFAULT nextval('roles_role_id_seq'::regclass);


--
-- Name: session_evaluation_id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE session_evaluation ALTER COLUMN session_evaluation_id SET DEFAULT nextval('session_evaluation_session_evaluation_id_seq'::regclass);


--
-- Name: subscriber_session_id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE subscribers_sessions ALTER COLUMN subscriber_session_id SET DEFAULT nextval('subscribers_sessions_subscriber_session_id_seq1'::regclass);


--
-- Name: user_role_id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE user_role ALTER COLUMN user_role_id SET DEFAULT nextval('user_role_user_role_id_seq'::regclass);


--
-- Name: useraudit_id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE useraudit ALTER COLUMN useraudit_id SET DEFAULT nextval('useraudit_useraudit_id_seq'::regclass);


SET search_path = feedback, pg_catalog;

--
-- Name: codes_pkey; Type: CONSTRAINT; Schema: feedback; Owner: -; Tablespace: 
--

ALTER TABLE ONLY codes
    ADD CONSTRAINT codes_pkey PRIMARY KEY (code_id);


--
-- Name: general_pkey; Type: CONSTRAINT; Schema: feedback; Owner: -; Tablespace: 
--

ALTER TABLE ONLY general
    ADD CONSTRAINT general_pkey PRIMARY KEY (id);


--
-- Name: logistics_pkey; Type: CONSTRAINT; Schema: feedback; Owner: -; Tablespace: 
--

ALTER TABLE ONLY logistics
    ADD CONSTRAINT logistics_pkey PRIMARY KEY (id);


--
-- Name: participant_pkey; Type: CONSTRAINT; Schema: feedback; Owner: -; Tablespace: 
--

ALTER TABLE ONLY participant
    ADD CONSTRAINT participant_pkey PRIMARY KEY (id);


--
-- Name: pkey; Type: CONSTRAINT; Schema: feedback; Owner: -; Tablespace: 
--

ALTER TABLE ONLY presentations
    ADD CONSTRAINT pkey PRIMARY KEY (id, presentation_id);


--
-- Name: programme_pkey; Type: CONSTRAINT; Schema: feedback; Owner: -; Tablespace: 
--

ALTER TABLE ONLY programme
    ADD CONSTRAINT programme_pkey PRIMARY KEY (id);


SET search_path = public, pg_catalog;

--
-- Name: conferences_abbreviation_key; Type: CONSTRAINT; Schema: public; Owner: -; Tablespace: 
--

ALTER TABLE ONLY conferences
    ADD CONSTRAINT conferences_abbreviation_key UNIQUE (abbreviation);


--
-- Name: conferences_hostname_key; Type: CONSTRAINT; Schema: public; Owner: -; Tablespace: 
--

ALTER TABLE ONLY conferences
    ADD CONSTRAINT conferences_hostname_key UNIQUE (hostname);


--
-- Name: conferences_pkey; Type: CONSTRAINT; Schema: public; Owner: -; Tablespace: 
--

ALTER TABLE ONLY conferences
    ADD CONSTRAINT conferences_pkey PRIMARY KEY (conference_id);


--
-- Name: deadlines_pkey; Type: CONSTRAINT; Schema: public; Owner: -; Tablespace: 
--

ALTER TABLE ONLY deadlines
    ADD CONSTRAINT deadlines_pkey PRIMARY KEY (deadline_id);


--
-- Name: eventcategories_pkey; Type: CONSTRAINT; Schema: public; Owner: -; Tablespace: 
--

ALTER TABLE ONLY eventcategories
    ADD CONSTRAINT eventcategories_pkey PRIMARY KEY (eventcategory_id);


--
-- Name: eventlog_pkey; Type: CONSTRAINT; Schema: public; Owner: -; Tablespace: 
--

ALTER TABLE ONLY eventlog
    ADD CONSTRAINT eventlog_pkey PRIMARY KEY (event_type, conference_id);


--
-- Name: events_pkey; Type: CONSTRAINT; Schema: public; Owner: -; Tablespace: 
--

ALTER TABLE ONLY events
    ADD CONSTRAINT events_pkey PRIMARY KEY (event_id);


--
-- Name: files_pkey; Type: CONSTRAINT; Schema: public; Owner: -; Tablespace: 
--

ALTER TABLE ONLY files
    ADD CONSTRAINT files_pkey PRIMARY KEY (file_id);


--
-- Name: filetypes_pkey; Type: CONSTRAINT; Schema: public; Owner: -; Tablespace: 
--

ALTER TABLE ONLY filetypes
    ADD CONSTRAINT filetypes_pkey PRIMARY KEY (filetype_id);


--
-- Name: locations_idx; Type: CONSTRAINT; Schema: public; Owner: -; Tablespace: 
--

ALTER TABLE ONLY locations
    ADD CONSTRAINT locations_idx UNIQUE (abbreviation, conference_id);


--
-- Name: locations_pkey; Type: CONSTRAINT; Schema: public; Owner: -; Tablespace: 
--

ALTER TABLE ONLY locations
    ADD CONSTRAINT locations_pkey PRIMARY KEY (location_id);


--
-- Name: posters_pkey; Type: CONSTRAINT; Schema: public; Owner: -; Tablespace: 
--

ALTER TABLE ONLY posters
    ADD CONSTRAINT posters_pkey PRIMARY KEY (poster_id);


--
-- Name: presentations_files_pkey; Type: CONSTRAINT; Schema: public; Owner: -; Tablespace: 
--

ALTER TABLE ONLY presentations_files
    ADD CONSTRAINT presentations_files_pkey PRIMARY KEY (presentation_file_id);


--
-- Name: presentations_pkey; Type: CONSTRAINT; Schema: public; Owner: -; Tablespace: 
--

ALTER TABLE ONLY presentations
    ADD CONSTRAINT presentations_pkey PRIMARY KEY (presentation_id);


--
-- Name: presentations_tags_pkey; Type: CONSTRAINT; Schema: public; Owner: -; Tablespace: 
--

ALTER TABLE ONLY presentations_tags
    ADD CONSTRAINT presentations_tags_pkey PRIMARY KEY (presentation_tag_id);


--
-- Name: presentations_users_pkey; Type: CONSTRAINT; Schema: public; Owner: -; Tablespace: 
--

ALTER TABLE ONLY presentations_users
    ADD CONSTRAINT presentations_users_pkey PRIMARY KEY (presentation_user_id);


--
-- Name: reviewers_submissions_pkey; Type: CONSTRAINT; Schema: public; Owner: -; Tablespace: 
--

ALTER TABLE ONLY reviewers_submissions
    ADD CONSTRAINT reviewers_submissions_pkey PRIMARY KEY (reviewer_submission_id);


--
-- Name: reviews_pkey; Type: CONSTRAINT; Schema: public; Owner: -; Tablespace: 
--

ALTER TABLE ONLY reviews
    ADD CONSTRAINT reviews_pkey PRIMARY KEY (review_id);


--
-- Name: roles_pkey; Type: CONSTRAINT; Schema: public; Owner: -; Tablespace: 
--

ALTER TABLE ONLY roles
    ADD CONSTRAINT roles_pkey PRIMARY KEY (role_id);


--
-- Name: session_evaluation_pkey; Type: CONSTRAINT; Schema: public; Owner: -; Tablespace: 
--

ALTER TABLE ONLY session_evaluation
    ADD CONSTRAINT session_evaluation_pkey PRIMARY KEY (session_evaluation_id);


--
-- Name: sessions_files_pkey; Type: CONSTRAINT; Schema: public; Owner: -; Tablespace: 
--

ALTER TABLE ONLY sessions_files
    ADD CONSTRAINT sessions_files_pkey PRIMARY KEY (session_file_id);


--
-- Name: sessions_idx; Type: CONSTRAINT; Schema: public; Owner: -; Tablespace: 
--

ALTER TABLE ONLY sessions
    ADD CONSTRAINT sessions_idx UNIQUE (location_id, timeslot_id);


--
-- Name: sessions_pkey; Type: CONSTRAINT; Schema: public; Owner: -; Tablespace: 
--

ALTER TABLE ONLY sessions
    ADD CONSTRAINT sessions_pkey PRIMARY KEY (session_id);


--
-- Name: sessions_presentations_pkey; Type: CONSTRAINT; Schema: public; Owner: -; Tablespace: 
--

ALTER TABLE ONLY sessions_presentations
    ADD CONSTRAINT sessions_presentations_pkey PRIMARY KEY (session_presentation_id);


--
-- Name: sessions_tags_pkey; Type: CONSTRAINT; Schema: public; Owner: -; Tablespace: 
--

ALTER TABLE ONLY sessions_tags
    ADD CONSTRAINT sessions_tags_pkey PRIMARY KEY (session_tag_id);


--
-- Name: sessions_users_pkey; Type: CONSTRAINT; Schema: public; Owner: -; Tablespace: 
--

ALTER TABLE ONLY sessions_users
    ADD CONSTRAINT sessions_users_pkey PRIMARY KEY (session_user_id);


--
-- Name: submission_state_pkey; Type: CONSTRAINT; Schema: public; Owner: -; Tablespace: 
--

ALTER TABLE ONLY submission_status
    ADD CONSTRAINT submission_state_pkey PRIMARY KEY (submission_status_id);


--
-- Name: submission_status_submission_id_key; Type: CONSTRAINT; Schema: public; Owner: -; Tablespace: 
--

ALTER TABLE ONLY submission_status
    ADD CONSTRAINT submission_status_submission_id_key UNIQUE (submission_id);


--
-- Name: submissions_pkey; Type: CONSTRAINT; Schema: public; Owner: -; Tablespace: 
--

ALTER TABLE ONLY submissions
    ADD CONSTRAINT submissions_pkey PRIMARY KEY (submission_id);


--
-- Name: suscribers_sessions_pkey; Type: CONSTRAINT; Schema: public; Owner: -; Tablespace: 
--

ALTER TABLE ONLY subscribers_sessions
    ADD CONSTRAINT suscribers_sessions_pkey PRIMARY KEY (subscriber_session_id);


--
-- Name: tags_pkey; Type: CONSTRAINT; Schema: public; Owner: -; Tablespace: 
--

ALTER TABLE ONLY tags
    ADD CONSTRAINT tags_pkey PRIMARY KEY (tag_id);


--
-- Name: timeslot_types_name_key; Type: CONSTRAINT; Schema: public; Owner: -; Tablespace: 
--

ALTER TABLE ONLY timeslot_types
    ADD CONSTRAINT timeslot_types_name_key UNIQUE (name);


--
-- Name: timeslot_types_pkey; Type: CONSTRAINT; Schema: public; Owner: -; Tablespace: 
--

ALTER TABLE ONLY timeslot_types
    ADD CONSTRAINT timeslot_types_pkey PRIMARY KEY (timeslot_type_id);


--
-- Name: timeslots_pkey; Type: CONSTRAINT; Schema: public; Owner: -; Tablespace: 
--

ALTER TABLE ONLY timeslots
    ADD CONSTRAINT timeslots_pkey PRIMARY KEY (timeslot_id);


--
-- Name: user_role_pkey; Type: CONSTRAINT; Schema: public; Owner: -; Tablespace: 
--

ALTER TABLE ONLY user_role
    ADD CONSTRAINT user_role_pkey PRIMARY KEY (user_role_id);


--
-- Name: useraudit_pkey; Type: CONSTRAINT; Schema: public; Owner: -; Tablespace: 
--

ALTER TABLE ONLY useraudit
    ADD CONSTRAINT useraudit_pkey PRIMARY KEY (useraudit_id);


--
-- Name: users_invite_key; Type: CONSTRAINT; Schema: public; Owner: -; Tablespace: 
--

ALTER TABLE ONLY users
    ADD CONSTRAINT users_invite_key UNIQUE (invite);


--
-- Name: users_pkey; Type: CONSTRAINT; Schema: public; Owner: -; Tablespace: 
--

ALTER TABLE ONLY users
    ADD CONSTRAINT users_pkey PRIMARY KEY (user_id);


--
-- Name: users_smart_id_key; Type: CONSTRAINT; Schema: public; Owner: -; Tablespace: 
--

ALTER TABLE ONLY users
    ADD CONSTRAINT users_smart_id_key UNIQUE (smart_id);


--
-- Name: users_submissions_pkey; Type: CONSTRAINT; Schema: public; Owner: -; Tablespace: 
--

ALTER TABLE ONLY users_submissions
    ADD CONSTRAINT users_submissions_pkey PRIMARY KEY (user_submission_id);


--
-- Name: events_idx; Type: INDEX; Schema: public; Owner: -; Tablespace: 
--

CREATE INDEX events_idx ON events USING btree (category_id);


--
-- Name: presentations_files_idx; Type: INDEX; Schema: public; Owner: -; Tablespace: 
--

CREATE UNIQUE INDEX presentations_files_idx ON presentations_files USING btree (presentation_id, file_id);


--
-- Name: presentations_users_idx; Type: INDEX; Schema: public; Owner: -; Tablespace: 
--

CREATE UNIQUE INDEX presentations_users_idx ON presentations_users USING btree (presentation_id, user_id);


--
-- Name: user_role_idx; Type: INDEX; Schema: public; Owner: -; Tablespace: 
--

CREATE UNIQUE INDEX user_role_idx ON user_role USING btree (user_id, role_id);


--
-- Name: events_tr; Type: TRIGGER; Schema: public; Owner: -
--

CREATE TRIGGER events_tr
    BEFORE UPDATE ON events
    FOR EACH STATEMENT
    EXECUTE PROCEDURE last_modified();

ALTER TABLE events DISABLE TRIGGER events_tr;


--
-- Name: last_modified; Type: TRIGGER; Schema: public; Owner: -
--

CREATE TRIGGER last_modified
    BEFORE UPDATE ON sessions
    FOR EACH ROW
    EXECUTE PROCEDURE last_modified();


--
-- Name: tg_fix_pres_order_del; Type: TRIGGER; Schema: public; Owner: -
--

CREATE TRIGGER tg_fix_pres_order_del
    AFTER DELETE ON sessions_presentations
    FOR EACH ROW
    EXECUTE PROCEDURE fix_order_on_del();


--
-- Name: tg_fix_pres_order_ins; Type: TRIGGER; Schema: public; Owner: -
--

CREATE TRIGGER tg_fix_pres_order_ins
    BEFORE INSERT ON sessions_presentations
    FOR EACH ROW
    EXECUTE PROCEDURE fix_order_on_insert();


--
-- Name: tg_last_modified; Type: TRIGGER; Schema: public; Owner: -
--

CREATE TRIGGER tg_last_modified
    BEFORE UPDATE ON session_evaluation
    FOR EACH ROW
    EXECUTE PROCEDURE last_modified();


--
-- Name: tg_last_modified; Type: TRIGGER; Schema: public; Owner: -
--

CREATE TRIGGER tg_last_modified
    BEFORE UPDATE ON presentations
    FOR EACH ROW
    EXECUTE PROCEDURE last_modified();


--
-- Name: tg_last_modified; Type: TRIGGER; Schema: public; Owner: -
--

CREATE TRIGGER tg_last_modified
    AFTER UPDATE ON posters
    FOR EACH ROW
    EXECUTE PROCEDURE last_modified();


SET search_path = feedback, pg_catalog;

--
-- Name: fk_feedback_id; Type: FK CONSTRAINT; Schema: feedback; Owner: -
--

ALTER TABLE ONLY logistics
    ADD CONSTRAINT fk_feedback_id FOREIGN KEY (id) REFERENCES codes(code_id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: fk_feedback_id; Type: FK CONSTRAINT; Schema: feedback; Owner: -
--

ALTER TABLE ONLY presentations
    ADD CONSTRAINT fk_feedback_id FOREIGN KEY (id) REFERENCES codes(code_id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: fk_feedback_id; Type: FK CONSTRAINT; Schema: feedback; Owner: -
--

ALTER TABLE ONLY programme
    ADD CONSTRAINT fk_feedback_id FOREIGN KEY (id) REFERENCES codes(code_id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: fk_id; Type: FK CONSTRAINT; Schema: feedback; Owner: -
--

ALTER TABLE ONLY general
    ADD CONSTRAINT fk_id FOREIGN KEY (id) REFERENCES codes(code_id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: fk_pres_id; Type: FK CONSTRAINT; Schema: feedback; Owner: -
--

ALTER TABLE ONLY presentations
    ADD CONSTRAINT fk_pres_id FOREIGN KEY (presentation_id) REFERENCES public.presentations(presentation_id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: participant_fk; Type: FK CONSTRAINT; Schema: feedback; Owner: -
--

ALTER TABLE ONLY participant
    ADD CONSTRAINT participant_fk FOREIGN KEY (id) REFERENCES codes(code_id) ON UPDATE CASCADE ON DELETE CASCADE;


SET search_path = public, pg_catalog;

--
-- Name: deadlines_fk; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY deadlines
    ADD CONSTRAINT deadlines_fk FOREIGN KEY (conference_id) REFERENCES conferences(conference_id) ON DELETE CASCADE;


--
-- Name: events_fk; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY events
    ADD CONSTRAINT events_fk FOREIGN KEY (location_id) REFERENCES locations(location_id) ON DELETE SET NULL;


--
-- Name: events_fk1; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY events
    ADD CONSTRAINT events_fk1 FOREIGN KEY (category_id) REFERENCES eventcategories(eventcategory_id);


--
-- Name: events_fk2; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY events
    ADD CONSTRAINT events_fk2 FOREIGN KEY (conference_id) REFERENCES conferences(conference_id) ON DELETE CASCADE;


--
-- Name: events_fk3; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY events
    ADD CONSTRAINT events_fk3 FOREIGN KEY (file_id) REFERENCES files(file_id);


--
-- Name: locations_fk; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY locations
    ADD CONSTRAINT locations_fk FOREIGN KEY (conference_id) REFERENCES conferences(conference_id);


--
-- Name: posters_fk; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY posters
    ADD CONSTRAINT posters_fk FOREIGN KEY (file_id) REFERENCES files(file_id);


--
-- Name: posters_fk1; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY posters
    ADD CONSTRAINT posters_fk1 FOREIGN KEY (conference_id) REFERENCES conferences(conference_id);


--
-- Name: presentations_files_fk; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY presentations_files
    ADD CONSTRAINT presentations_files_fk FOREIGN KEY (presentation_id) REFERENCES presentations(presentation_id) ON DELETE CASCADE;


--
-- Name: presentations_files_fk1; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY presentations_files
    ADD CONSTRAINT presentations_files_fk1 FOREIGN KEY (file_id) REFERENCES files(file_id);


--
-- Name: presentations_fk; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY presentations
    ADD CONSTRAINT presentations_fk FOREIGN KEY (submission_id) REFERENCES submissions(submission_id);


--
-- Name: presentations_fk1; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY presentations
    ADD CONSTRAINT presentations_fk1 FOREIGN KEY (conference_id) REFERENCES conferences(conference_id) ON DELETE CASCADE;


--
-- Name: presentations_tags_fk; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY presentations_tags
    ADD CONSTRAINT presentations_tags_fk FOREIGN KEY (presentation_id) REFERENCES presentations(presentation_id) ON DELETE CASCADE;


--
-- Name: presentations_tags_fk1; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY presentations_tags
    ADD CONSTRAINT presentations_tags_fk1 FOREIGN KEY (tag_id) REFERENCES tags(tag_id);


--
-- Name: presentations_users_fk; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY presentations_users
    ADD CONSTRAINT presentations_users_fk FOREIGN KEY (presentation_id) REFERENCES presentations(presentation_id) ON DELETE CASCADE;


--
-- Name: presentations_users_fk1; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY presentations_users
    ADD CONSTRAINT presentations_users_fk1 FOREIGN KEY (user_id) REFERENCES users(user_id);


--
-- Name: reviewers_submissions_fk; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY reviewers_submissions
    ADD CONSTRAINT reviewers_submissions_fk FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE;


--
-- Name: reviewers_submissions_fk1; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY reviewers_submissions
    ADD CONSTRAINT reviewers_submissions_fk1 FOREIGN KEY (submission_id) REFERENCES submissions(submission_id) ON DELETE CASCADE;


--
-- Name: reviews_fk; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY reviews
    ADD CONSTRAINT reviews_fk FOREIGN KEY (submission_id) REFERENCES submissions(submission_id) ON DELETE CASCADE;


--
-- Name: reviews_fk1; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY reviews
    ADD CONSTRAINT reviews_fk1 FOREIGN KEY (user_id) REFERENCES users(user_id);


--
-- Name: session_evaluation_fk; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY session_evaluation
    ADD CONSTRAINT session_evaluation_fk FOREIGN KEY (session_id) REFERENCES sessions(session_id) ON DELETE CASCADE;


--
-- Name: session_evaluation_fk1; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY session_evaluation
    ADD CONSTRAINT session_evaluation_fk1 FOREIGN KEY (user_id) REFERENCES users(user_id);


--
-- Name: sessions_files_fk; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY sessions_files
    ADD CONSTRAINT sessions_files_fk FOREIGN KEY (session_id) REFERENCES sessions(session_id) ON DELETE CASCADE;


--
-- Name: sessions_files_fk1; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY sessions_files
    ADD CONSTRAINT sessions_files_fk1 FOREIGN KEY (file_id) REFERENCES files(file_id);


--
-- Name: sessions_fk; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY sessions
    ADD CONSTRAINT sessions_fk FOREIGN KEY (location_id) REFERENCES locations(location_id);


--
-- Name: sessions_fk1; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY sessions
    ADD CONSTRAINT sessions_fk1 FOREIGN KEY (timeslot_id) REFERENCES timeslots(timeslot_id);


--
-- Name: sessions_fk2; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY sessions
    ADD CONSTRAINT sessions_fk2 FOREIGN KEY (conference_id) REFERENCES conferences(conference_id);


--
-- Name: sessions_presentations_fk; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY sessions_presentations
    ADD CONSTRAINT sessions_presentations_fk FOREIGN KEY (session_id) REFERENCES sessions(session_id) ON DELETE CASCADE;


--
-- Name: sessions_presentations_fk1; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY sessions_presentations
    ADD CONSTRAINT sessions_presentations_fk1 FOREIGN KEY (presentation_id) REFERENCES presentations(presentation_id) ON DELETE CASCADE;


--
-- Name: sessions_tags_fk; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY sessions_tags
    ADD CONSTRAINT sessions_tags_fk FOREIGN KEY (session_id) REFERENCES sessions(session_id) ON DELETE CASCADE;


--
-- Name: sessions_tags_fk1; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY sessions_tags
    ADD CONSTRAINT sessions_tags_fk1 FOREIGN KEY (tag_id) REFERENCES tags(tag_id);


--
-- Name: sessions_users_fk; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY sessions_users
    ADD CONSTRAINT sessions_users_fk FOREIGN KEY (session_id) REFERENCES sessions(session_id) ON DELETE CASCADE;


--
-- Name: sessions_users_fk1; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY sessions_users
    ADD CONSTRAINT sessions_users_fk1 FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE;


--
-- Name: submission_status_fk; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY submission_status
    ADD CONSTRAINT submission_status_fk FOREIGN KEY (submission_id) REFERENCES submissions(submission_id) ON DELETE CASCADE;


--
-- Name: submission_status_fk1; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY submission_status
    ADD CONSTRAINT submission_status_fk1 FOREIGN KEY (session_id) REFERENCES sessions(session_id) ON DELETE SET NULL;


--
-- Name: submissions_fk; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY submissions
    ADD CONSTRAINT submissions_fk FOREIGN KEY (file_id) REFERENCES files(file_id);


--
-- Name: submissions_fk1; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY submissions
    ADD CONSTRAINT submissions_fk1 FOREIGN KEY (conference_id) REFERENCES conferences(conference_id);


--
-- Name: suscribers_sessions_fk; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY subscribers_sessions
    ADD CONSTRAINT suscribers_sessions_fk FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE;


--
-- Name: suscribers_sessions_fk1; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY subscribers_sessions
    ADD CONSTRAINT suscribers_sessions_fk1 FOREIGN KEY (session_id) REFERENCES sessions(session_id) ON DELETE CASCADE;


--
-- Name: timeslots_fk; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY timeslots
    ADD CONSTRAINT timeslots_fk FOREIGN KEY (conference_id) REFERENCES conferences(conference_id) ON DELETE CASCADE;


--
-- Name: user_role_fk; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY user_role
    ADD CONSTRAINT user_role_fk FOREIGN KEY (user_id) REFERENCES users(user_id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: user_role_fk1; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY user_role
    ADD CONSTRAINT user_role_fk1 FOREIGN KEY (role_id) REFERENCES roles(role_id);


--
-- Name: user_role_fk2; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY user_role
    ADD CONSTRAINT user_role_fk2 FOREIGN KEY (conf_id) REFERENCES conferences(conference_id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: useraudit_fk; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY useraudit
    ADD CONSTRAINT useraudit_fk FOREIGN KEY (user_id) REFERENCES users(user_id);


--
-- Name: users_fk; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY users
    ADD CONSTRAINT users_fk FOREIGN KEY (file_id) REFERENCES files(file_id);


--
-- Name: users_submissions_fk; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY users_submissions
    ADD CONSTRAINT users_submissions_fk FOREIGN KEY (user_id) REFERENCES users(user_id);


--
-- Name: users_submissions_fk1; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY users_submissions
    ADD CONSTRAINT users_submissions_fk1 FOREIGN KEY (submission_id) REFERENCES submissions(submission_id) ON DELETE CASCADE;


--
-- PostgreSQL database dump complete
--

INSERT INTO filetypes (filetype_id, name) VALUES (1, 'submission');
INSERT INTO filetypes (filetype_id, name) VALUES (2, 'userimage');
INSERT INTO filetypes (filetype_id, name) VALUES (3, 'misc');
INSERT INTO filetypes (filetype_id, name) VALUES (4, 'paper');
INSERT INTO filetypes (filetype_id, name) VALUES (5, 'slides');
INSERT INTO filetypes (filetype_id, name) VALUES (6, 'poster');
INSERT INTO filetypes (filetype_id, name) VALUES (7, 'location');
INSERT INTO roles (role_id, name) VALUES (3, 'reviewer');
INSERT INTO roles (role_id, name) VALUES (2, 'user');
INSERT INTO roles (role_id, name) VALUES (999, 'admin');
INSERT INTO roles (role_id, name) VALUES (4, 'submitter');
INSERT INTO roles (role_id, name) VALUES (5, 'presenter');
INSERT INTO roles (role_id, name) VALUES (6, 'chair');
INSERT INTO timeslot_types (timeslot_type_id, name) VALUES (1, 'presentations');
INSERT INTO timeslot_types (timeslot_type_id, name) VALUES (2, 'coffee break');
INSERT INTO timeslot_types (timeslot_type_id, name) VALUES (3, 'lunch');
