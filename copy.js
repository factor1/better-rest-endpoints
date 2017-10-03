const fs = require('fs-extra'),
      target = '/Users/ericstout/Local Sites/reacttestwp/app/public/wp-content/plugins/better-wp-endpoints',
      files = './';

fs.copy(files, target)
  .then(() => console.log('Files copied successfully!'))
  .catch(err => console.error(err))
