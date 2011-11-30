-- SQL patch to upgrade from CORE 0.9.1 to 0.9.2

CREATE TABLE reviewbreaker (
	submission_id INTEGER NOT NULL, 
	evalue REAL NOT NULL, 
	CONSTRAINT reviewbreaker_pkey PRIMARY KEY(submission_id), 
	CONSTRAINT reviewbreaker_submission_id_fk1
	FOREIGN KEY (submission_id)
	REFERENCES submissions(submission_id)
	ON DELETE CASCADE
	ON UPDATE NO ACTION
	NOT DEFERRABLE
) WITHOUT OIDS;


ALTER TABLE reviewers_submissions
	ADD COLUMN tiebreaker
	BOOLEAN
	DEFAULT false
	NOT NULL;