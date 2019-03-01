CREATE TABLE public.logs (
    id SERIAL PRIMARY KEY,
    title character varying(245),
    platform character varying(245),
    action character varying(245),
    results text,
    execute_time smallint,
    total_time smallint,
    item_id SERIAL NOT NULL,
    user_id SERIAL NOT NULL,
    error boolean,
    created_at timestamp without time zone,
    updated_at timestamp without time zone
);
