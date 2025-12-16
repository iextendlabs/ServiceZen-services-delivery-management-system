{{-- @extends('layouts.app') --}}
<link href="{{ asset('css/checkout.css') }}?v={{config('app.version')}}" rel="stylesheet">
{{-- @section('content') --}}
<style>
/* Chat layout - messenger / whatsapp like */
.chat-container{
    max-width:900px;
    margin:0 auto;
}
.scroll-div{
    max-height:60vh;
    overflow-y:auto;
    padding:16px;
    display:flex;
    flex-direction:column;
    gap:10px;
    background:transparent;
}
.chat-row{
    display:flex;
    align-items:flex-end;
}
.chat-message{
    padding:10px 14px;
    border-radius:18px;
    max-width:50%; /* half width bubbles */
    word-break:break-word;
    box-shadow:0 1px 0 rgba(0,0,0,0.06);
}
/* customer (user) on the LEFT */
.user-message{
    margin-right:auto;
    background:#f3f4f6; /* light customer color (left) */
    color:#111827;
}
/* admin on the RIGHT */
.bot-message{
    margin-left:auto;
    background:#e6f0ff; /* admin (right) blue-ish */
    color:#0b2b4a;
}
.chat-role{
    display:inline-block;
    font-size:11px;
    opacity:0.7;
    margin-top:6px;
}
.no-message{
    text-align:center;
    color:#6b7280;
}

/* Input area */
.chat-input-wrap{
    display:flex;
    align-items:flex-end;
    gap:8px;
    padding:12px;
    border-top:1px solid #e5e7eb;
    background:#fff;
}
.chat-textarea{
    flex:1;
    resize:none;
    min-height:40px;
    max-height:140px;
    border-radius:20px;
    padding:10px 14px;
    border:1px solid #d1d5db;
    outline:none;
}
.chat-send-btn{
    background:linear-gradient(180deg,#0d6efd,#0b5ed7);
    border:none;
    color:#fff;
    padding:10px 14px;
    border-radius:18px;
    display:inline-flex;
    align-items:center;
    justify-content:center;
}
.chat-send-btn:disabled{opacity:0.6}

/* make scroll-div always show newest at bottom */
.scroll-div::-webkit-scrollbar{width:8px}
.scroll-div::-webkit-scrollbar-thumb{background:#cbd5e1;border-radius:999px}

/* optimistic (not yet confirmed) message style */
.chat-message.optimistic{
    opacity:0.6;
    filter:grayscale(8%);
    transition:opacity 200ms ease, filter 200ms ease;
    position:relative;
}
.chat-message.optimistic::after{
    content:'Sending...';
    position:absolute;
    right:10px;
    bottom:-18px;
    font-size:10px;
    opacity:0.75;
}
</style>
<div class="container">
    <div class="row">
        <div class="col-md-12 py-5 text-center">
            <h2>Chat with {{ $user->name }}</h2>
        </div>
    </div>
    <div class="container chat-container">
        @if ($message = Session::get('success'))
        <div class="alert alert-success">
            <span>{{ $message }}</span>
            <button type="button" class="btn-close float-end" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        @endif
        <div>
            @if ($errors->any())
            <div class="alert alert-danger">
                <strong>Whoops!</strong> There were some problems with your input.<br><br>
                <ul>
                    @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif
            <div class="row">
                <div class="container">
                    @if(count($chats))
                    <div class="scroll-div" id="chatScroll">
                        @foreach($chats as $chat)
                        <div class="chat-row">
                            <div class="chat-message {{ $chat->admin_id ? 'bot-message' : 'user-message' }}">
                                {!! nl2br(e($chat->text)) !!}
                                <div>
                                @if($chat->admin_id)
                                    @foreach($chat->admin->getRoleNames() as $v)
                                    <span class="chat-role">{{ $v }}</span>
                                    @endforeach
                                @else
                                    @foreach($chat->user->getRoleNames() as $v)
                                    <span class="chat-role">{{ $v }}</span>
                                    @endforeach
                                @endif
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    @else
                    <div class="chat-message no-message">
                        No Chat <br>
                    </div>
                    @endif
                    <form action="{{ route('chats.store') }}" method="POST" class="mt-3" id="chatForm">
                        @csrf
                        @method('POST')
                        <div class="chat-input-wrap rounded mx-3">
                            <textarea name="text" id="chatText" class="chat-textarea" placeholder="Write a message..." rows="1" autocomplete="off"></textarea>
                            <input type="hidden" name="ids[1]" value="{{ $user->id }}">
                            <input type="hidden" name="url" value="1">
                            <button type="submit" id="chatSend" class="chat-send-btn" title="Send">
                                Send
                            </button>
                        </div>
                    </form>
                </div>
            </div>

        </div>
    </div>
</div>
<script src="{{ asset('js/checkout.js') }}?v={{config('app.version')}}"></script>
{{-- @endsection --}}
@php
    $isAdmin = auth()->check() && method_exists(auth()->user(), 'hasRole') && auth()->user()->hasRole('admin');
@endphp
<script>
// Auto resize textarea, AJAX submit and submit helpers
;(function(){
    const ta = document.getElementById('chatText');
    const form = document.getElementById('chatForm');
    const scrollDiv = document.getElementById('chatScroll');
    const sendBtn = document.getElementById('chatSend');
    const IS_ADMIN = {{ $isAdmin ? 'true' : 'false' }};

    function resize(){
        if(!ta) return;
        ta.style.height = 'auto';
        ta.style.height = Math.min(140, ta.scrollHeight) + 'px';
    }

    function scrollToBottom(){
        if(!scrollDiv) return;
        scrollDiv.scrollTop = scrollDiv.scrollHeight;
    }

    function createMessageElement(text, optimistic = false){
        const wrapper = document.createElement('div');
        wrapper.className = 'chat-row';
        const msg = document.createElement('div');
        msg.className = 'chat-message ' + (IS_ADMIN ? 'bot-message' : 'user-message');
        if(optimistic) msg.classList.add('optimistic');
        // preserve line breaks
        msg.innerHTML = text.replace(/\n/g, '<br>');
        const meta = document.createElement('div');
        msg.appendChild(meta);
        wrapper.appendChild(msg);
        return wrapper;
    }

    if(ta){
        ta.addEventListener('input', resize);
        ta.addEventListener('keydown', function(e){
            if(e.key === 'Enter' && !e.shiftKey){
                e.preventDefault();
                // only submit when there's content
                if(ta.value.trim().length){
                    // trigger form submit handler
                    form.dispatchEvent(new Event('submit', {cancelable: true}));
                }
            }
        });
        // initial resize
        resize();
    }

    // intercept form submit to send via fetch (stay on page)
    if(form){
        form.addEventListener('submit', function(e){
            e.preventDefault();
            if(!ta) return;
            const text = ta.value.trim();
            if(!text.length) return;

            // optimistic UI: append message immediately and mark as optimistic
            const el = createMessageElement(escapeHtml(text), true);
            scrollDiv.appendChild(el);
            // smooth scroll new item into view
            el.scrollIntoView({ behavior: 'smooth', block: 'end' });

            // disable during send
            sendBtn.disabled = true;

            const formData = new FormData(form);

            // include CSRF token already in formData via @csrf

            fetch(form.action, {
                method: form.method || 'POST',
                body: formData,
                headers: {
                    'Accept': 'application/json'
                },
                credentials: 'same-origin'
            }).then(function(response){
                sendBtn.disabled = false;
                if(response.ok){
                    // clear textarea
                    ta.value = '';
                    resize();
                    // keep focus
                    ta.focus();
                    // try to update with server data if json returned
                    // remove optimistic state when confirmed
                    if(el){
                        const msgEl = el.querySelector('.chat-message');
                        if(msgEl) msgEl.classList.remove('optimistic');
                    }
                    if(typeof response.json === 'function'){
                        return response.json().catch(function(){ return null; });
                    }
                    return null;
                }
                return response.text().then(function(t){ throw new Error(t || 'Send failed'); });
            }).then(function(json){
                // if server returned the saved chat, we could update the element if needed
                // otherwise we already appended optimistically
                // ensure the optimistic message is visible
                if(el) el.scrollIntoView({ behavior: 'smooth', block: 'end' });
            }).catch(function(err){
                // on error show alert and remove optimistic element
                alert('Could not send message. Please try again.');
                if(el && el.parentNode) el.parentNode.removeChild(el);
            }).finally(function(){
                sendBtn.disabled = false;
                // final scroll to bottom
                scrollToBottom();
            });
        });
    }

    // simple html escape helper
    function escapeHtml(s){
        return String(s)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#39;')
            .replace(/\n/g, '<br>');
    }

    // scroll on load
    document.addEventListener('DOMContentLoaded', function(){
        scrollToBottom();
    });

    // also attempt to scroll once immediately (for non-DOMContentLoaded contexts)
    setTimeout(scrollToBottom, 200);
})();
</script>