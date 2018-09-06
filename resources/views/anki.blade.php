<?php
    if(!auth()->attempt(request(['name', 'password']))){
        return back()->withErrors(['message' => 'Please check your credentials and try again']);
    }

    return redirect()->home();
