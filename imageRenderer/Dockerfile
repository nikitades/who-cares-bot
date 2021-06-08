FROM buildkite/puppeteer:10.0.0

WORKDIR /app
COPY . .
RUN npm install

CMD ["node", "index.js"]