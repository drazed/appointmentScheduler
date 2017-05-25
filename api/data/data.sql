INSERT INTO users
  (name, email, password)
VALUES
  ('Foo', 'foo@host.com', '$2y$10$34w0udB8WTSKEzkaRzgmHev8Lx5EcK07Fs.SMZXnNc8w3yNPUXjNW'),
  ('Bar', 'bar@host.com', '$2y$10$9wTSa.QrGxP9Q3zjLC74cebwA1ro5a7JOzvFHnSCApPDoutRfvGmW');

INSERT INTO appointments
  (name, reason, date, start, end)
VALUES
  ('Jane Smith', 'Checkup', '2017-06-01', '14:15', '14:45'),
  ('John Doe', 'Sick', '2017-06-01', '14:45', '16:15');
