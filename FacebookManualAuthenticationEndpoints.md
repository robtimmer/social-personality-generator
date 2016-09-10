# Dialog to grant profile access to the app
- https://www.facebook.com/dialog/oauth?client_id=1769515346629314&scope=public_profile,email,user_about_me,user_posts&redirect_uri=http://localhost
# Response that contains the access token (expires in 2 hours)
- https://graph.facebook.com/v2.7/oauth/access_token?
client_id=1769515346629314&redirect_uri=http://localhost/&
client_secret=<YOUR_APP_SECRET>&code=<AUTHORIZATION_CODE>