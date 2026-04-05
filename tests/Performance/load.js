import http from 'k6/http';
import { check, sleep } from 'k6';

export const options = {
  stages: [
    { duration: '30s', target: 20 }, // ramp up to 20 users
    { duration: '1m', target: 20 },  // stay at 20 users
    { duration: '30s', target: 0 },  // ramp down
  ],
};

export default function () {
  const res = http.get('http://localhost:8000/');
  check(res, {
    'status is 200': (r) => r.status === 200,
  });
  sleep(1);
}
