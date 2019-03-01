CREATE TABLE public.users (
    id SERIAL PRIMARY KEY,
    email character varying(245) NOT NULL,
    password character varying(245) NOT NULL,
    role character varying(20),
    config TEXT,
    api_key character varying(245),
    hash character varying(245),
    token character varying(245),
    password_reset_hash character varying(128),
    password_reset_request_date timestamp without time zone,
    active boolean,
    confirm boolean,
    created_at timestamp without time zone,
    updated_at timestamp without time zone
);
