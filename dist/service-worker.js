self.addEventListener('fetch', function(event) {
  // Check if the request is for a protected resource
  if (event.request.url.startsWith('https://admin.kutt.my') ||
      event.request.url.startsWith('https://kiter.test')
      ) {
    // Serve the resource directly without caching
    event.respondWith(
      fetch(event.request, { cache: "no-store" })
        .then(function(networkResponse) {
          if (networkResponse.status === 401) {
            // Redirect the user to the login page
            return Response.redirect('/auth/signin');
          }
          return networkResponse;
        })
    );
  }
});