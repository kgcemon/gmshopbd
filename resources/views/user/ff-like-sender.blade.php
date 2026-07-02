@extends('user.master')

@section('title', "Free Fire Free Like daily 100 | Codzshop")

@section('meta_description', 'Free Fire Like daily 100 full free use Codzshop no need any payment use full free with 100 ff Like')
@section('meta_keywords', 'Codzshop,ff like')

@section('content')
    <main style="padding: 30px 15px;">
        @auth()
                <div class="panelData" style="max-width: 450px; margin: auto; position: relative;">
                    <h2 style="margin-bottom:20px; text-align:center;">ফ্রি ফায়ার ১০০ লাইক প্রতিদিন</h2>

                    <!-- Response Message -->
                    <div id="responseMessage" style="display:none; margin-bottom:15px;"></div>

                    <!-- Loader -->
                    <div id="loader" style="display:none; position:absolute; top:0; left:0; right:0; bottom:0;
             background:rgba(0,0,0,0.6); border-radius:12px; align-items:center; justify-content:center; flex-direction:column; z-index:10; color:#fff; font-weight:600; font-size:14px;">
                        <div class="spinner" style="border:4px solid rgba(255,255,255,0.2); border-top:4px solid #00d4ff;
                 border-radius:50%; width:40px; height:40px; animation:spin 1s linear infinite; margin-bottom:10px;">
                        </div>
                        <div>Loading... <span id="loadingCount">0</span></div>
                    </div>

                    <form id="likeForm" method="POST">
                        @csrf
                        <div style="margin-bottom: 15px; text-align: left;">
                            <label for="player_id" style="font-weight:600;">Player ID</label>
                            <input type="text" id="player_id" name="player_id" class="form-control"
                                   placeholder="Enter your Player ID" required
                                   style="width:100%;padding:10px;border-radius:8px;border:none;outline:none;margin-top:5px;">
                        </div>

                        <div style="margin-bottom: 15px; text-align: left;">
                            <label for="region" style="font-weight:600;">Select Region</label>
                            <select id="region" name="region" required
                                    style="width:100%;padding:10px;border-radius:8px;border:none;outline:none;margin-top:5px;">
                                <option value="">-- Choose Region --</option>
                                <option value="sg">BD</option>
                                <option value="me">ME</option>
                                <option value="th">TH</option>
                                <option value="vn">VN</option>
                                <option value="us">US</option>
                                <option value="br">BR</option>
                                <option value="sac">SAC</option>
                            </select>
                        </div>

                        <button type="submit"
                                style="width:100%;padding:12px;background:linear-gradient(135deg,#00c6ff,#0072ff);
                border:none;border-radius:8px;color:white;font-weight:600;cursor:pointer;transition:.3s;">
                            Submit
                        </button>
                    </form>
                </div>
            @endauth
    </main>

    <!-- Loader & Animations -->
    <style>
        @keyframes spin {
            0% { transform: rotate(0deg);}
            100% { transform: rotate(360deg);}
        }
        @keyframes pulse {
            0% { transform: scale(1); opacity: 0.8; }
            50% { transform: scale(1.2); opacity: 1; }
            100% { transform: scale(1); opacity: 0.8; }
        }
        #loadingCount { animation: pulse 1s infinite; }

        /* Response card styles */
        #responseMessage .card {
            width: 100%;
            border-radius: 20px;
            padding: 25px;
            background: rgba(40, 167, 69, 0.9);
            color: #fff;
            text-shadow: 0 1px 3px rgba(0,0,0,0.4);
            box-shadow: 0 15px 35px rgba(0,0,0,0.4);
            backdrop-filter: blur(8px);
            -webkit-backdrop-filter: blur(8px);
            font-family: 'Arial', sans-serif;
            position: relative;
            overflow: hidden;
        }
        #responseMessage .card.failed {
            background: rgba(220, 53, 69, 0.9);
        }
        #responseMessage .card .site-brand {
            font-size: 20px;
            font-weight: 700;
            margin-bottom: 15px;
            text-align: center;
            letter-spacing: 1px;
        }
        #responseMessage .card .player-info {
            margin-bottom: 10px;
            font-size: 14px;
        }
        #responseMessage .card .likes {
            font-size: 16px;
            font-weight: 600;
            margin: 10px 0;
        }
        #responseMessage .card .footer {
            margin-top: 15px;
            font-size: 12px;
            opacity: 0.8;
            text-align: center;
        }
        /* Share/Download buttons */
        .share-download {
            display: flex;
            justify-content: center;
            gap: 15px;
            margin-top: 15px;
        }
        .share-download button {
            padding: 8px 15px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 600;
            transition: 0.3s;
        }
        .share-download .download {
            background: #00d4ff;
            color: #fff;
        }
        .share-download .share {
            background: #20c997;
            color: #fff;
        }
    </style>

    <!-- Include html2canvas -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>

    <script>
        let countInterval;

        document.getElementById("likeForm").addEventListener("submit", function(e) {
            e.preventDefault();

            let form = this;
            let loader = document.getElementById("loader");
            let responseMessage = document.getElementById("responseMessage");
            let loadingCount = document.getElementById("loadingCount");

            let playerId = form.player_id.value.trim();
            if (!/^[0-9]+$/.test(playerId)) {
                showMessage("Player ID অবশ্যই শুধু সংখ্যা হবে!", true);
                return;
            }
            if (playerId.length < 5 || playerId.length > 13) {
                showMessage("Player ID কমপক্ষে 5 digit এবং সর্বোচ্চ 13 digit হতে হবে!", true);
                return;
            }

            let counter = 0;
            loadingCount.textContent = counter;
            loader.style.display = "flex";
            responseMessage.style.display = "none";

            countInterval = setInterval(() => {
                counter++;
                loadingCount.textContent = counter;
            }, 1000);

            fetch("{{ route('player.submit') }}", {
                method: "POST",
                headers: {
                    "X-CSRF-TOKEN": "{{ csrf_token() }}",
                    "Accept": "application/json",
                    "Content-Type": "application/json"
                },
                body: JSON.stringify({
                    player_id: playerId,
                    region: form.region.value
                })
            })
                .then(res => res.json())
                .then(data => {
                    loader.style.display = "none";
                    clearInterval(countInterval);

                    if (data.success && data.data) {
                        let d = data.data;

                        if (d.failed_likes === 0) {
                            showMessage("আপনি আজকে আর লাইক নিতে পারবেন না, আগামিকাল চেষ্টা করুন ধন্যবাদ।", true);
                        }
                        else if (typeof d.likes_added !== "undefined") {
                            let added = d.likes_after - d.likes_before;
                            let nextTry = "ফ্রি ফায়ার ডায়মন্ড টপ আপ করতে এখনি ভিজিট করুন Codzshop.com";

                            let html = `
                    <div class="card" id="successCard">
                        <span class="site-brand" style="">Codzshop.Com</span>
                        <div class="player-info"><strong>Name:</strong> ${d.name}</div>
                        <div class="player-info"><strong>Region:</strong> ${d.region}</div>
                        <div class="player-info"><strong>Level:</strong> ${d.level}</div>
                        <div class="likes"><strong>Likes Before:</strong> ${d.likes_before}</div>
                        <div class="likes"><strong>Likes Added:</strong> ${added}</div>
                        <div class="likes"><strong>Likes After:</strong> ${d.likes_after}</div>
                        <div class="footer">${added === 0 ? "⚠️ আজ কোনো লাইক যোগ হয়নি।" : ""} ${nextTry}</div>
                        <div class="share-download">
                            <button class="download">Download</button>
                            <button class="share">Share</button>
                        </div>
                    </div>
                `;
                            showMessage(html, false);

                            // Add download functionality
                            document.querySelector('#successCard .download').addEventListener('click', () => {
                                html2canvas(document.getElementById('successCard')).then(canvas => {
                                    let link = document.createElement('a');
                                    link.download = `player_${d.player_id}.png`;
                                    link.href = canvas.toDataURL("image/png");
                                    link.click();
                                });
                            });

                            // Simple share functionality
                            document.querySelector('#successCard .share').addEventListener('click', () => {
                                if (navigator.share) {
                                    html2canvas(document.getElementById('successCard')).then(canvas => {
                                        canvas.toBlob(blob => {
                                            const file = new File([blob], `player_${d.player_id}.png`, {type: 'image/png'});
                                            navigator.share({
                                                files: [file],
                                                title: 'My Player Info',
                                                text: 'Check out my player stats!',
                                            });
                                        });
                                    });
                                } else {
                                    alert("Your browser does not support sharing.");
                                }
                            });
                        } else {
                            showMessage("Server error, please try again.", true);
                        }
                    } else {
                        showMessage("Something went wrong!", true);
                    }
                })
                .catch(err => {
                    loader.style.display = "none";
                    clearInterval(countInterval);
                    showMessage("Server error, please try again.", true);
                });

            function showMessage(msg, failed){
                responseMessage.style.display = "block";
                responseMessage.innerHTML = failed ? `<div class="card failed">${msg}</div>` : msg;
            }
        });
    </script>
@endsection
